<?php

namespace App\Console\Commands;

use App\Services\RightEntitlementStagingImportService;
use Illuminate\Console\Command;
use Throwable;

class ImportRightEntitlementStaging extends Command
{
    protected $signature =
        'dcfms:stage-right-entitlement
        {file : Absolute path or project-relative path to the XLSX file}
        {--batch= : Unique migration batch code}';

    protected $description =
        'Stage and validate the Law & Law Enforcement Right and Entitlement workbook without creating DCFMS cases.';

    public function handle(
        RightEntitlementStagingImportService $importer
    ): int {
        $inputPath =
            (string) $this->argument(
                'file'
            );

        $filePath =
            $this->resolvePath(
                $inputPath
            );

        $batchCode =
            (string) (
                $this->option('batch')
                ?: 'LAW-RE-' .
                    now()->format(
                        'Ymd-His'
                    )
            );

        $this->info(
            'Starting staging import...'
        );

        $this->line(
            "File: {$filePath}"
        );

        $this->line(
            "Batch: {$batchCode}"
        );

        try {
            $batch =
                $importer->import(
                    $filePath,
                    $batchCode,
                    auth()->id()
                );
        } catch (Throwable $exception) {
            $this->error(
                $exception->getMessage()
            );

            return self::FAILURE;
        }

        $this->newLine();

        $this->table(
            [
                'Metric',
                'Count',
            ],
            [
                [
                    'Total staged',
                    $batch->total_records,
                ],
                [
                    'Valid',
                    $batch->validated_records,
                ],
                [
                    'Manual review',
                    $batch->manual_review_records,
                ],
                [
                    'Duplicate review',
                    $batch->duplicate_records,
                ],
                [
                    'Invalid',
                    $batch->invalid_records,
                ],
            ]
        );

        $this->newLine();

        $this->info(
            "Batch status: {$batch->status}"
        );

        $this->warn(
            'No DCFMS operational cases were created. Records exist only in staging_case_imports.'
        );

        return self::SUCCESS;
    }

    private function resolvePath(
        string $path
    ): string {
        if (is_file($path)) {
            return $path;
        }

        $projectRelative =
            base_path($path);

        if (is_file($projectRelative)) {
            return $projectRelative;
        }

        return $path;
    }
}
