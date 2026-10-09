<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalCaseDetail extends Model
{
    protected $fillable = [
        'dcfms_case_id',
        're_number',
        'legal_officer_id',
        'investigation_officer_id',
        'legal_status',
        'inquiry_started_date',
        'observation_requested_date',
        'observation_due_date',
        'first_reminder_date',
        'second_reminder_date',
        'field_visit_required',
        'field_visit_date',
        'io_findings',
        'legal_recommendation',
        'case_conference_required',
        'case_conference_date',
        'case_conference_outcome',
        'board_submission_required',
        'board_submission_date',
        'board_decision',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'inquiry_started_date' => 'date',
            'observation_requested_date' => 'date',
            'observation_due_date' => 'date',
            'first_reminder_date' => 'date',
            'second_reminder_date' => 'date',
            'field_visit_required' => 'boolean',
            'field_visit_date' => 'date',
            'case_conference_required' => 'boolean',
            'case_conference_date' => 'date',
            'board_submission_required' => 'boolean',
            'board_submission_date' => 'date',
        ];
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(DcfmsCase::class, 'dcfms_case_id');
    }

    public function legalOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'legal_officer_id');
    }

    public function investigationOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'investigation_officer_id');
    }
}