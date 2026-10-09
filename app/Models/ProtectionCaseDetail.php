<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProtectionCaseDetail extends Model
{
    protected $fillable = [
        'dcfms_case_id',
        'protection_officer_id',
        'protection_status',
        'threat_type',
        'threat_assessment_requested_date',
        'police_reference',
        'local_police_station',
        'interim_protection_required',
        'interim_protection_requested_date',
        'threat_assessment_status',
        'threat_assessment_received_date',
        'threat_level',
        'threat_assessment_summary',
        'protection_decision',
        'protection_start_date',
        'review_date',
        'protection_outcome',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'threat_assessment_requested_date' => 'date',
            'interim_protection_required' => 'boolean',
            'interim_protection_requested_date' => 'date',
            'threat_assessment_received_date' => 'date',
            'protection_start_date' => 'date',
            'review_date' => 'date',
        ];
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(DcfmsCase::class, 'dcfms_case_id');
    }

    public function protectionOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'protection_officer_id');
    }
}