<?php

namespace App\Services;

use App\Models\DcfmsCase;
use Carbon\CarbonInterface;

class CaseAgingService
{
    /*
    |--------------------------------------------------------------------------
    | Case Age
    |--------------------------------------------------------------------------
    |
    | Number of days from the received date until today or closure.
    |
    */

    public function ageDays(
        DcfmsCase $case
    ): int {
        $start =
            $case->received_date
            ?? $case->created_at;

        if (!$start) {
            return 0;
        }

        $end =
            $case->closed_at
            ?? now();

        return $start
            ->copy()
            ->startOfDay()
            ->diffInDays(
                $end
                    ->copy()
                    ->startOfDay()
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Last Recorded Activity
    |--------------------------------------------------------------------------
    */

    public function lastActivityAt(
        DcfmsCase $case
    ): ?CarbonInterface {
        if (
            $case->relationLoaded(
                'statusHistory'
            )
        ) {
            $lastHistory =
                $case
                    ->statusHistory
                    ->sortByDesc(
                        'created_at'
                    )
                    ->first();

            return
                $lastHistory?->created_at
                ?? $case->updated_at
                ?? $case->created_at;
        }


        $lastHistory =
            $case
                ->statusHistory()
                ->latest(
                    'created_at'
                )
                ->first();


        return
            $lastHistory?->created_at
            ?? $case->updated_at
            ?? $case->created_at;
    }


    /*
    |--------------------------------------------------------------------------
    | Days Since Last Activity
    |--------------------------------------------------------------------------
    */

    public function inactiveDays(
        DcfmsCase $case
    ): int {
        $lastActivity =
            $this->lastActivityAt(
                $case
            );

        if (!$lastActivity) {
            return 0;
        }

        return $lastActivity
            ->copy()
            ->startOfDay()
            ->diffInDays(
                now()
                    ->startOfDay()
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Follow-Up Review Threshold
    |--------------------------------------------------------------------------
    |
    | This is not a completion deadline or SLA.
    |
    */

    public function reviewThresholdDays(): int
    {
        return (int) config(
            'dcfms.case_monitoring.activity_review_after_days',
            7
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Follow-Up Review Suggested
    |--------------------------------------------------------------------------
    |
    | Closed cases are excluded from activity follow-up monitoring.
    |
    */

    public function requiresFollowUp(
        DcfmsCase $case
    ): bool {
        if (
            $case->current_status ===
            'Closed'
        ) {
            return false;
        }

        return
            $this->inactiveDays(
                $case
            )
            >=
            $this->reviewThresholdDays();
    }


    /*
    |--------------------------------------------------------------------------
    | Activity Status
    |--------------------------------------------------------------------------
    */

    public function activityStatus(
        DcfmsCase $case
    ): string {
        if (
            $case->current_status ===
            'Closed'
        ) {
            return 'closed';
        }

        if (
            $this->requiresFollowUp(
                $case
            )
        ) {
            return 'follow_up';
        }

        return 'active';
    }


    /*
    |--------------------------------------------------------------------------
    | Human-Readable Activity Label
    |--------------------------------------------------------------------------
    */

    public function activityLabel(
        DcfmsCase $case
    ): string {
        if (
            $case->current_status ===
            'Closed'
        ) {
            return 'Closed';
        }


        if (
            $this->requiresFollowUp(
                $case
            )
        ) {
            return 'Follow-up review suggested';
        }


        $inactiveDays =
            $this->inactiveDays(
                $case
            );


        if ($inactiveDays === 0) {
            return 'Activity recorded today';
        }


        if ($inactiveDays === 1) {
            return 'Last activity 1 day ago';
        }


        return
            "Last activity {$inactiveDays} days ago";
    }


    /*
    |--------------------------------------------------------------------------
    | Full Case Aging / Activity Summary
    |--------------------------------------------------------------------------
    */

    public function summary(
        DcfmsCase $case
    ): array {
        $lastActivity =
            $this->lastActivityAt(
                $case
            );


        return [

            'age_days' =>
                $this->ageDays(
                    $case
                ),

            'last_activity_at' =>
                $lastActivity,

            'inactive_days' =>
                $this->inactiveDays(
                    $case
                ),

            'review_threshold_days' =>
                $this->reviewThresholdDays(),

            'requires_follow_up' =>
                $this->requiresFollowUp(
                    $case
                ),

            'activity_status' =>
                $this->activityStatus(
                    $case
                ),

            'label' =>
                $this->activityLabel(
                    $case
                ),
        ];
    }
}