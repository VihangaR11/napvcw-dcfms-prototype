<?php

namespace App\Services;

use App\Models\CaseMigrationRecord;
use App\Models\MigrationBatch;
use App\Models\StagingCaseImport;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use RuntimeException;
use Throwable;

class RightEntitlementStagingImportService
{
    private const DATASET = 'right_entitlement';

    private const DIVISION = 'Law and Law Enforcement';

    private const CATEGORY = 'Rights / Entitlement Violation';

    private const YEAR_SHEETS = [
        '2017',
        '2018',
        '2019',
        '2020',
        '2021',
        '2022',
        '2023',
        '2024',
        '2025',
        '2026',
    ];

    public function import(
        string $filePath,
        string $batchCode,
        ?int $createdBy = null
    ): MigrationBatch {
        if (!is_file($filePath)) {
            throw new RuntimeException(
                "Import file not found: {$filePath}"
            );
        }

        if (MigrationBatch::query()
            ->where('batch_code', $batchCode)
            ->exists()) {
            throw new RuntimeException(
                "Migration batch code already exists: {$batchCode}"
            );
        }

        $batch = MigrationBatch::create([
            'batch_code' => $batchCode,
            'source_division' => self::DIVISION,
            'source_dataset' => self::DATASET,
            'source_file' => basename($filePath),
            'status' => 'staging',
            'created_by' => $createdBy,
        ]);

        try {
            $spreadsheet = IOFactory::load($filePath);

            foreach (self::YEAR_SHEETS as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);

                if (!$sheet) {
                    $this->addBatchNote(
                        $batch,
                        "Worksheet {$sheetName} was not found."
                    );

                    continue;
                }

                $highestRow = $sheet->getHighestDataRow();

                /*
                |--------------------------------------------------------------------------
                | Row 1 is the header.
                |--------------------------------------------------------------------------
                |
                | We deliberately use fixed source columns A:J rather than matching
                | header text. The Authority's workbook contains small header-format
                | differences, but the year sheets retain this same column order.
                |
                */

                for ($row = 2; $row <= $highestRow; $row++) {
                    $raw = [
                        'date_of_complaint' =>
                            $sheet->getCell("A{$row}")->getValue(),

                        'complaint_number' =>
                            $sheet->getCell("B{$row}")->getValue(),

                        'complainant_name' =>
                            $sheet->getCell("C{$row}")->getValue(),

                        'complainant_address' =>
                            $sheet->getCell("D{$row}")->getValue(),

                        'district' =>
                            $sheet->getCell("E{$row}")->getValue(),

                        'province' =>
                            $sheet->getCell("F{$row}")->getValue(),

                        'gender' =>
                            $sheet->getCell("G{$row}")->getValue(),

                        'alleged_offence' =>
                            $sheet->getCell("H{$row}")->getValue(),

                        'current_status' =>
                            $sheet->getCell("I{$row}")->getValue(),

                        'closing_date' =>
                            $sheet->getCell("J{$row}")->getValue(),
                    ];

                    if ($this->rowIsEmpty($raw)) {
                        continue;
                    }

                    $this->stageRow(
                        $batch,
                        $sheetName,
                        $row,
                        $raw
                    );
                }
            }

            $this->markDuplicates($batch);

            $this->refreshBatchCounts($batch);

            $batch->update([
                'status' =>
                    $batch->manual_review_records > 0
                        || $batch->duplicate_records > 0
                        || $batch->invalid_records > 0
                            ? 'review_required'
                            : 'ready',

                'validated_at' => now(),
            ]);

            return $batch->fresh();
        } catch (Throwable $exception) {
            $batch->update([
                'status' => 'failed',
                'notes' => trim(
                    ($batch->notes ? $batch->notes . PHP_EOL : '') .
                    'Import failed: ' . $exception->getMessage()
                ),
            ]);

            throw $exception;
        }
    }

    private function stageRow(
        MigrationBatch $batch,
        string $sheetName,
        int $rowNumber,
        array $raw
    ): void {
        $notes = [];

        $legacyNumber =
            $this->cleanString(
                $raw['complaint_number']
            );

        $name =
            $this->cleanString(
                $raw['complainant_name']
            );

        $legacyStatus =
            $this->cleanString(
                $raw['current_status']
            );

        $receivedDateResult =
            $this->parseDate(
                $raw['date_of_complaint']
            );

        $closingDateResult =
            $this->parseDate(
                $raw['closing_date']
            );

        if (!$legacyNumber) {
            $notes[] =
                'Missing legacy complaint number.';
        }

        if (!$name) {
            $notes[] =
                'Missing complainant name.';
        }

        if (
            $raw['date_of_complaint'] !== null
            && !$receivedDateResult['date']
        ) {
            $notes[] =
                'Invalid complaint date: ' .
                $receivedDateResult['raw'];
        }

        if (
            $raw['date_of_complaint'] === null
            || $this->cleanString(
                $raw['date_of_complaint']
            ) === null
        ) {
            $notes[] =
                'Complaint date is missing.';
        }

        if (
            $raw['closing_date'] !== null
            && !$closingDateResult['date']
        ) {
            $notes[] =
                'Invalid closing date: ' .
                $closingDateResult['raw'];
        }

        $statusLooksClosed =
            $this->statusLooksClosed(
                $legacyStatus
            );

        if (
            $statusLooksClosed
            && !$closingDateResult['date']
        ) {
            $notes[] =
                'Status indicates closed but no valid closing date is available.';
        }

        if (
            !$statusLooksClosed
            && $closingDateResult['date']
        ) {
            $notes[] =
                'Closing date exists but Current Status does not clearly indicate closure.';
        }

        $legalStatus =
            $this->normalizeLegalStatus(
                $legacyStatus
            );

        if (
            $legacyStatus
            && $legalStatus ===
                'Received by Legal Division'
            && !$statusLooksClosed
        ) {
            $notes[] =
                'Legacy Current Status could not be mapped confidently to a structured legal status.';
        }

        $criticalInvalid =
            !$legacyNumber
            || !$name;

        $requiresReview =
            !empty($notes);

        StagingCaseImport::create([
            'migration_batch_id' =>
                $batch->id,

            'source_dataset' =>
                self::DATASET,

            'source_file' =>
                $batch->source_file,

            'source_sheet' =>
                $sheetName,

            'source_row_number' =>
                $rowNumber,

            'legacy_case_number' =>
                $legacyNumber,

            'legacy_year' =>
                (int) $sheetName,

            'received_date_raw' =>
                $receivedDateResult['raw'],

            'complainant_name_raw' =>
                $name,

            'complainant_address_raw' =>
                $this->cleanString(
                    $raw['complainant_address']
                ),

            'district_raw' =>
                $this->cleanString(
                    $raw['district']
                ),

            'province_raw' =>
                $this->cleanString(
                    $raw['province']
                ),

            'gender_raw' =>
                $this->cleanString(
                    $raw['gender']
                ),

            'legacy_status_raw' =>
                $legacyStatus,

            'closing_date_raw' =>
                $closingDateResult['raw'],

            /*
            |--------------------------------------------------------------------------
            | Alleged offence
            |--------------------------------------------------------------------------
            |
            | The provided Right & Entitlement workbook contains this column but the
            | dataset is currently empty. We deliberately do not generate a value.
            |
            */

            'received_date_normalized' =>
                $receivedDateResult['date'],

            'complainant_name_normalized' =>
                $name,

            'district_normalized' =>
                $this->normalizeDistrict(
                    $raw['district']
                ),

            'province_normalized' =>
                $this->normalizeProvince(
                    $raw['province']
                ),

            'gender_normalized' =>
                $this->normalizeGender(
                    $raw['gender']
                ),

            'complaint_category_normalized' =>
                self::CATEGORY,

            'primary_division_normalized' =>
                self::DIVISION,

            'legal_status_normalized' =>
                $legalStatus,

            'closing_date_normalized' =>
                $closingDateResult['date'],

            'migration_status' =>
                $criticalInvalid
                    ? 'invalid'
                    : (
                        $requiresReview
                            ? 'manual_review'
                            : 'ready'
                    ),

            'validation_notes' =>
                empty($notes)
                    ? null
                    : implode(
                        PHP_EOL,
                        array_unique($notes)
                    ),

            'requires_manual_review' =>
                $requiresReview,

            'is_duplicate' =>
                false,

            'is_valid' =>
                !$criticalInvalid,
        ]);
    }

    private function markDuplicates(
        MigrationBatch $batch
    ): void {
        $duplicates =
            StagingCaseImport::query()
                ->where(
                    'migration_batch_id',
                    $batch->id
                )
                ->whereNotNull(
                    'legacy_case_number'
                )
                ->select(
                    'legacy_case_number'
                )
                ->groupBy(
                    'legacy_case_number'
                )
                ->havingRaw(
                    'COUNT(*) > 1'
                )
                ->pluck(
                    'legacy_case_number'
                );

        /*
        |--------------------------------------------------------------------------
        | Duplicate Inside This Workbook
        |--------------------------------------------------------------------------
        */

        if ($duplicates->isNotEmpty()) {
            StagingCaseImport::query()
                ->where(
                    'migration_batch_id',
                    $batch->id
                )
                ->whereIn(
                    'legacy_case_number',
                    $duplicates
                )
                ->get()
                ->each(
                    fn (StagingCaseImport $row) =>
                        $this->flagDuplicate(
                            $row,
                            'Duplicate legacy case number exists within this migration batch.'
                        )
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Previously Migrated Legacy Number
        |--------------------------------------------------------------------------
        */

        $previouslyMigrated =
            CaseMigrationRecord::query()
                ->whereNotNull(
                    'legacy_case_number'
                )
                ->pluck(
                    'legacy_case_number'
                )
                ->filter()
                ->unique()
                ->values();

        if ($previouslyMigrated->isNotEmpty()) {
            StagingCaseImport::query()
                ->where(
                    'migration_batch_id',
                    $batch->id
                )
                ->whereIn(
                    'legacy_case_number',
                    $previouslyMigrated
                )
                ->get()
                ->each(
                    fn (StagingCaseImport $row) =>
                        $this->flagDuplicate(
                            $row,
                            'Legacy case number has already been migrated in an earlier batch.'
                        )
                );
        }
    }

    private function flagDuplicate(
        StagingCaseImport $row,
        string $message
    ): void {
        $notes =
            trim(
                ($row->validation_notes
                    ? $row->validation_notes .
                        PHP_EOL
                    : '') .
                $message
            );

        $row->update([
            'is_duplicate' => true,
            'requires_manual_review' => true,
            'migration_status' => 'duplicate_review',
            'validation_notes' => $notes,
        ]);
    }

    private function refreshBatchCounts(
        MigrationBatch $batch
    ): void {
        $query =
            StagingCaseImport::query()
                ->where(
                    'migration_batch_id',
                    $batch->id
                );

        $batch->update([
            'total_records' =>
                (clone $query)->count(),

            'validated_records' =>
                (clone $query)
                    ->where(
                        'is_valid',
                        true
                    )
                    ->count(),

            'manual_review_records' =>
                (clone $query)
                    ->where(
                        'requires_manual_review',
                        true
                    )
                    ->count(),

            'duplicate_records' =>
                (clone $query)
                    ->where(
                        'is_duplicate',
                        true
                    )
                    ->count(),

            'invalid_records' =>
                (clone $query)
                    ->where(
                        'migration_status',
                        'invalid'
                    )
                    ->count(),
        ]);
    }

    private function parseDate(
        mixed $value
    ): array {
        if ($value === null || $value === '') {
            return [
                'raw' => null,
                'date' => null,
            ];
        }

        if ($value instanceof DateTimeInterface) {
            return [
                'raw' =>
                    $value->format(
                        'Y-m-d'
                    ),

                'date' =>
                    Carbon::instance(
                        $value
                    )->toDateString(),
            ];
        }

        if (
            is_numeric($value)
            && (float) $value > 0
        ) {
            try {
                $date =
                    ExcelDate::excelToDateTimeObject(
                        (float) $value
                    );

                return [
                    'raw' =>
                        (string) $value,

                    'date' =>
                        Carbon::instance(
                            $date
                        )->toDateString(),
                ];
            } catch (Throwable) {
                // Continue to string parsing.
            }
        }

        $raw =
            trim(
                (string) $value
            );

        $formats = [
            'd/m/Y',
            'm/d/Y',
            'Y/m/d',
            'Y-m-d',
            'd-m-Y',
            'd.m.Y',
            'Y.m.d',
        ];

        foreach ($formats as $format) {
            try {
                $date =
                    Carbon::createFromFormat(
                        '!' . $format,
                        $raw
                    );

                /*
                |--------------------------------------------------------------------------
                | Strict Validation
                |--------------------------------------------------------------------------
                |
                | Carbon/PHP may normalize impossible dates such as 31/09/2024.
                | Formatting the parsed value back and comparing it with the source
                | prevents those records from being silently "corrected".
                |
                */

                if (
                    $date
                        ->format($format)
                    !==
                    $raw
                ) {
                    continue;
                }

                return [
                    'raw' => $raw,
                    'date' =>
                        $date->toDateString(),
                ];
            } catch (Throwable) {
                // Try the next format.
            }
        }

        return [
            'raw' => $raw,
            'date' => null,
        ];
    }

    private function normalizeLegalStatus(
        ?string $status
    ): string {
        if (!$status) {
            return
                'Received by Legal Division';
        }

        $value =
            mb_strtolower(
                $status
            );

        if (
            str_contains(
                $value,
                'closed'
            )
        ) {
            return 'Closed';
        }

        if (
            str_contains(
                $value,
                'awaiting board'
            )
        ) {
            return
                'Awaiting Board Decision';
        }

        if (
            str_contains(
                $value,
                'submitted to board'
            )
        ) {
            return
                'Submitted to Board';
        }

        if (
            str_contains(
                $value,
                'case conference'
            )
        ) {
            if (
                str_contains(
                    $value,
                    'recommend'
                )
            ) {
                return
                    'Case Conference Recommended';
            }

            if (
                str_contains(
                    $value,
                    'schedule'
                )
            ) {
                return
                    'Case Conference Scheduled';
            }

            return
                'Case Conference Conducted';
        }

        if (
            str_contains(
                $value,
                'second reminder'
            )
        ) {
            return
                'Second Reminder Sent';
        }

        if (
            str_contains(
                $value,
                'first reminder'
            )
        ) {
            return
                'First Reminder Sent';
        }

        if (
            str_contains(
                $value,
                'reminder'
            )
        ) {
            /*
             * A generic reminder cannot reliably tell us whether it is the
             * first or second reminder, so we intentionally avoid guessing.
             */
            return
                'Received by Legal Division';
        }

        if (
            str_contains(
                $value,
                'awaiting observation'
            )
        ) {
            return
                'Awaiting Observation';
        }

        if (
            str_contains(
                $value,
                'observation'
            )
        ) {
            return
                'Observation Requested';
        }

        if (
            str_contains(
                $value,
                'investigation'
            )
        ) {
            return
                'Investigation In Progress';
        }

        if (
            str_contains(
                $value,
                'legal review'
            )
        ) {
            return
                'Legal Review';
        }

        return
            'Received by Legal Division';
    }

    private function statusLooksClosed(
        ?string $status
    ): bool {
        if (!$status) {
            return false;
        }

        return
            str_contains(
                mb_strtolower($status),
                'closed'
            );
    }

    private function normalizeDistrict(
        mixed $value
    ): ?string {
        $value =
            $this->cleanString(
                $value
            );

        if (!$value) {
            return null;
        }

        $key =
            mb_strtolower(
                preg_replace(
                    '/\s+/',
                    '',
                    $value
                )
            );

        $map = [
            'mathale' => 'Matale',
            'matale' => 'Matale',
            'mathara' => 'Matara',
            'matara' => 'Matara',
            'kaluthara' => 'Kalutara',
            'katutara' => 'Kalutara',
            'kalutara' => 'Kalutara',
            'rathnapura' => 'Ratnapura',
            'rathnepura' => 'Ratnapura',
            'ratnapura' => 'Ratnapura',
            'nuwaraeliya' => 'Nuwara Eliya',
            'nuwaraliya' => 'Nuwara Eliya',
        ];

        return
            $map[$key]
            ?? trim($value);
    }

    private function normalizeProvince(
        mixed $value
    ): ?string {
        $value =
            $this->cleanString(
                $value
            );

        if (!$value) {
            return null;
        }

        $key =
            mb_strtolower(
                trim($value)
            );

        $map = [
            'south' => 'Southern',
            'southern' => 'Southern',
            'western' => 'Western',
            'central' => 'Central',
            'eastern' => 'Eastern',
            'northern' => 'Northern',
            'north western' =>
                'North Western',
            'northwestern' =>
                'North Western',
            'north central' =>
                'North Central',
            'northcentral' =>
                'North Central',
            'uva' => 'Uva',
            'sabaragamuwa' =>
                'Sabaragamuwa',
        ];

        return
            $map[$key]
            ?? trim($value);
    }

    private function normalizeGender(
        mixed $value
    ): ?string {
        $value =
            $this->cleanString(
                $value
            );

        if (!$value) {
            return null;
        }

        return match (
            mb_strtolower($value)
        ) {
            'male', 'm' =>
                'Male',

            'female', 'f' =>
                'Female',

            'other' =>
                'Other',

            default =>
                trim($value),
        };
    }

    private function cleanString(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return
                $value->format(
                    'Y-m-d'
                );
        }

        $value =
            trim(
                preg_replace(
                    '/\s+/u',
                    ' ',
                    (string) $value
                )
            );

        return
            $value === ''
                ? null
                : $value;
    }

    private function rowIsEmpty(
        array $row
    ): bool {
        foreach ($row as $value) {
            if (
                $value !== null
                && trim((string) $value) !== ''
            ) {
                return false;
            }
        }

        return true;
    }

    private function addBatchNote(
        MigrationBatch $batch,
        string $note
    ): void {
        $batch->update([
            'notes' =>
                trim(
                    ($batch->notes
                        ? $batch->notes .
                            PHP_EOL
                        : '') .
                    $note
                ),
        ]);
    }
}
