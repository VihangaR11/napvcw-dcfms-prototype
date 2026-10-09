<?php

namespace App\Services;

use App\Enums\CaseStatus;
use App\Models\CaseStatusHistory;
use App\Models\DcfmsCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class CaseLifecycleService
{
    public function __construct(
        private readonly AuditLogService $audit
    ) {
    }

    private const TRANSITIONS = [
        'Registered' => [
            'Routed',
        ],

        'Routed' => [
            'Under Processing',
        ],

        'Under Processing' => [
            'Awaiting Decision',
            'Closed',
        ],

        'Awaiting Decision' => [
            'Under Processing',
            'Closed',
        ],

        'Closed' => [
            'Under Processing',
        ],
    ];

    public function canTransition(
        DcfmsCase $case,
        CaseStatus $target
    ): bool {
        $current = $case->current_status;

        if ($current === $target->value) {
            return true;
        }

        return in_array(
            $target->value,
            self::TRANSITIONS[$current] ?? [],
            true
        );
    }

    public function allowedTransitions(
        DcfmsCase $case
    ): array {
        return self::TRANSITIONS[
            $case->current_status
        ] ?? [];
    }

    public function transition(
        DcfmsCase $case,
        CaseStatus $target,
        User $user,
        string $action,
        ?string $remarks = null,
        ?string $division = null
    ): DcfmsCase {

        return DB::transaction(
            function () use (
                $case,
                $target,
                $user,
                $action,
                $remarks,
                $division
            ) {
                $lockedCase =
                    DcfmsCase::query()
                        ->whereKey($case->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                $previousStatus =
                    $lockedCase->current_status;

                $previousClosedAt =
                    $lockedCase->closed_at;

                /*
                |--------------------------------------------------------------------------
                | Same-state request
                |--------------------------------------------------------------------------
                |
                | Normally idempotent. If the case already says Closed but the
                | timestamp is missing, repair the inconsistent master record.
                |
                */

                if ($previousStatus === $target->value) {
                    if (
                        $target === CaseStatus::CLOSED &&
                        empty($lockedCase->closed_at)
                    ) {
                        DB::table('dcfms_cases')
                            ->where(
                                'id',
                                $lockedCase->id
                            )
                            ->update([
                                'closed_at' => now(),
                                'updated_at' => now(),
                            ]);

                        $lockedCase->refresh();
                    }

                    return $lockedCase;
                }

                if (
                    !$this->canTransition(
                        $lockedCase,
                        $target
                    )
                ) {
                    throw ValidationException::withMessages([
                        'current_status' =>
                            'Invalid case status transition from ' .
                            "{$previousStatus} to {$target->value}.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Authoritative master-case update
                |--------------------------------------------------------------------------
                |
                | The Case Registry reads dcfms_cases.current_status, therefore the
                | lifecycle service must update the master case row itself.
                |
                */

                $updates = [
                    'current_status' =>
                        $target->value,

                    'updated_at' =>
                        now(),
                ];

                if ($target === CaseStatus::CLOSED) {
                    $updates['closed_at'] = now();
                } elseif (
                    $previousStatus ===
                    CaseStatus::CLOSED->value
                ) {
                    $updates['closed_at'] = null;
                }

                /*
                |--------------------------------------------------------------------------
                | Direct system-controlled update
                |--------------------------------------------------------------------------
                |
                | Using the query builder here avoids lifecycle persistence being
                | affected by model mass-assignment configuration.
                |
                */

                DB::table('dcfms_cases')
                    ->where(
                        'id',
                        $lockedCase->id
                    )
                    ->update($updates);

                $lockedCase->refresh();

                /*
                |--------------------------------------------------------------------------
                | Persistence checks
                |--------------------------------------------------------------------------
                */

                if (
                    $lockedCase->current_status !==
                    $target->value
                ) {
                    throw new RuntimeException(
                        'Case lifecycle status did not persist correctly.'
                    );
                }

                if (
                    $target === CaseStatus::CLOSED &&
                    empty($lockedCase->closed_at)
                ) {
                    throw new RuntimeException(
                        'Closed case timestamp did not persist correctly.'
                    );
                }

                if (
                    $previousStatus ===
                        CaseStatus::CLOSED->value &&
                    $target !==
                        CaseStatus::CLOSED &&
                    !empty($lockedCase->closed_at)
                ) {
                    throw new RuntimeException(
                        'Reopened case still contains a closed timestamp.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Human-readable case timeline
                |--------------------------------------------------------------------------
                */

                CaseStatusHistory::create([
                    'dcfms_case_id' =>
                        $lockedCase->id,

                    'status' =>
                        $target->value,

                    'action_taken' =>
                        $action,

                    'remarks' =>
                        $this->buildRemarks(
                            $previousStatus,
                            $target->value,
                            $remarks
                        ),

                    'division' =>
                        $division,

                    'updated_by' =>
                        $user->id,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Structured audit log
                |--------------------------------------------------------------------------
                */

                $oldValues = [
                    'current_status' =>
                        $previousStatus,
                ];

                $newValues = [
                    'current_status' =>
                        $target->value,
                ];

                if (
                    $target ===
                        CaseStatus::CLOSED ||
                    $previousStatus ===
                        CaseStatus::CLOSED->value
                ) {
                    $oldValues['closed_at'] =
                        $previousClosedAt
                            ? $previousClosedAt->toDateTimeString()
                            : null;

                    $newValues['closed_at'] =
                        $lockedCase->closed_at
                            ? $lockedCase->closed_at->toDateTimeString()
                            : null;
                }

                $this->audit->record(
                    'CASE_STATUS_CHANGED',
                    $lockedCase,
                    $oldValues,
                    $newValues,
                    $this->buildRemarks(
                        $previousStatus,
                        $target->value,
                        $remarks
                    ),
                    'Case Lifecycle',
                    $lockedCase->id
                );

                return $lockedCase->fresh();
            }
        );
    }

    private function buildRemarks(
        string $previousStatus,
        string $newStatus,
        ?string $remarks
    ): string {
        $message =
            'Case status changed from ' .
            "{$previousStatus} to {$newStatus}.";

        if (filled($remarks)) {
            $message .=
                ' ' .
                trim($remarks);
        }

        return $message;
    }
}
