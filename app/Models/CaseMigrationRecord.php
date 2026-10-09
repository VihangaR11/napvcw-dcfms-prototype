<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseMigrationRecord extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'migrated_at' => 'datetime',
        ];
    }

    public function dcfmsCase(): BelongsTo
    {
        return $this->belongsTo(
            DcfmsCase::class,
            'dcfms_case_id'
        );
    }

    public function migrationBatch(): BelongsTo
    {
        return $this->belongsTo(
            MigrationBatch::class
        );
    }

    public function stagingCaseImport(): BelongsTo
    {
        return $this->belongsTo(
            StagingCaseImport::class
        );
    }
}
