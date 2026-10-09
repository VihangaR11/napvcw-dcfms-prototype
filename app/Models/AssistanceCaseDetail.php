<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssistanceCaseDetail extends Model
{
    protected $fillable = [
        'dcfms_case_id',
        'assistance_status',
        'assistance_type',
        'referral_required',
        'referred_to',
        'referral_date',
        'current_action',
        'follow_up_date',
        'assistance_outcome',
        'completed_date',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'referral_required' => 'boolean',
            'referral_date' => 'date',
            'follow_up_date' => 'date',
            'completed_date' => 'date',
        ];
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(DcfmsCase::class, 'dcfms_case_id');
    }

}