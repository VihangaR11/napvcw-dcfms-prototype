<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
            'closed_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CaseAssignment::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(CaseStatusHistory::class)
            ->orderBy('created_at', 'desc');
    }
}