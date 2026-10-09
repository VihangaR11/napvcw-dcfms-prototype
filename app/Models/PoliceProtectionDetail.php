<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoliceProtectionDetail extends Model
{
    protected $fillable = [
        'dcfms_case_id',
        'police_protection_officer_id',
        'assessment_status',
        'request_received_date',
        'assessment_started_date',
        'assessment_completed_date',
        'threat_level',
        'assessment_summary',
        'recommendation',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'request_received_date' => 'date',
            'assessment_started_date' => 'date',
            'assessment_completed_date' => 'date',
        ];
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(DcfmsCase::class, 'dcfms_case_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'police_protection_officer_id');
    }
}