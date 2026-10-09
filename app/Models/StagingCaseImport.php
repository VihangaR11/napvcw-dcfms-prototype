<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StagingCaseImport extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'legacy_year' => 'integer',
            'source_row_number' => 'integer',
            'received_date_normalized' => 'date',
            'closing_date_normalized' => 'date',
            'requires_manual_review' => 'boolean',
            'is_duplicate' => 'boolean',
            'is_valid' => 'boolean',
            'migrated_at' => 'datetime',
        ];
    }

    public function migrationBatch(): BelongsTo
    {
        return $this->belongsTo(MigrationBatch::class);
    }

    public function dcfmsCase(): BelongsTo
    {
        return $this->belongsTo(DcfmsCase::class, 'dcfms_case_id');
    }
}
