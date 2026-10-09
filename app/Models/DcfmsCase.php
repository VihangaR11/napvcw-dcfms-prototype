<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DcfmsCase extends Model
{
    protected $table = 'dcfms_cases';

    protected $fillable = [
        'case_number',
        'received_date',
        'complaint_source',
        'complaint_mode',
        'complainant_name',
        'victim_witness_type',
        'contact_number',
        'email',
        'complaint_summary',
        'complaint_category',
        'urgency',
        'current_status',
        'primary_division',
        'created_by',
        'closed_at',

        /*
        |--------------------------------------------------------------------------
        | Director - Police Protection Threat Recommendation
        |--------------------------------------------------------------------------
        */

        'threat_assessment_status',
        'threat_assessment_by',
        'threat_assessment_at',

        /*
        |--------------------------------------------------------------------------
        | Director General Protection Decision
        |--------------------------------------------------------------------------
        */

        'dg_protection_type',
        'dg_protection_decided_by',
        'dg_protection_decided_at',

        /*
        |--------------------------------------------------------------------------
        | Interim Protection
        |--------------------------------------------------------------------------
        */

        'interim_protection_approved',
        'interim_protection_approved_by',
        'interim_protection_approved_at',

        /*
        |--------------------------------------------------------------------------
        | Final Protection Approval
        |--------------------------------------------------------------------------
        */

        'approved_protection_types',
        'final_protection_approved_by',
        'final_protection_approved_at',
    ];

    protected function casts(): array
    {
        return [
            'received_date' =>
                'date',

            'closed_at' =>
                'datetime',

            'threat_assessment_at' =>
                'datetime',

            'dg_protection_decided_at' =>
                'datetime',

            'interim_protection_approved' =>
                'boolean',

            'interim_protection_approved_at' =>
                'datetime',

            'approved_protection_types' =>
                'array',

            'final_protection_approved_at' =>
                'datetime',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Case Creator
    |--------------------------------------------------------------------------
    */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Case Assignments
    |--------------------------------------------------------------------------
    */

    public function assignments(): HasMany
    {
        return $this->hasMany(
            CaseAssignment::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Case Status History
    |--------------------------------------------------------------------------
    */

    public function statusHistory(): HasMany
    {
        return $this->hasMany(
            CaseStatusHistory::class
        )
            ->orderBy(
                'created_at',
                'desc'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Legal Workflow
    |--------------------------------------------------------------------------
    */

    public function legalDetail(): HasOne
    {
        return $this->hasOne(
            LegalCaseDetail::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Protection Services Workflow
    |--------------------------------------------------------------------------
    */

    public function protectionDetail(): HasOne
    {
        return $this->hasOne(
            ProtectionCaseDetail::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Police Protection Workflow
    |--------------------------------------------------------------------------
    */

    public function policeProtectionDetail(): HasOne
    {
        return $this->hasOne(
            PoliceProtectionDetail::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Assistance Services Workflow
    |--------------------------------------------------------------------------
    */

    public function assistanceDetail(): HasOne
    {
        return $this->hasOne(
            AssistanceCaseDetail::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Director - Police Protection Recommendation
    |--------------------------------------------------------------------------
    */

    public function threatAssessmentDirector(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'threat_assessment_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Director General Protection Decision
    |--------------------------------------------------------------------------
    */

    public function dgProtectionDecisionMaker(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'dg_protection_decided_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Interim Protection Approver
    |--------------------------------------------------------------------------
    */

    public function interimProtectionApprover(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'interim_protection_approved_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Final Protection Approver
    |--------------------------------------------------------------------------
    */

    public function finalProtectionApprover(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'final_protection_approved_by'
        );
    }
}