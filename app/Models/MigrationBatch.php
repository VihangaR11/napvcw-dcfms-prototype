<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MigrationBatch extends Model
{
    protected $fillable = [
        'batch_code',
        'source_division',
        'source_dataset',
        'source_file',
        'status',
        'total_records',
        'validated_records',
        'manual_review_records',
        'duplicate_records',
        'invalid_records',
        'imported_records',
        'failed_records',
        'notes',
        'created_by',
        'validated_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_records' => 'integer',
            'validated_records' => 'integer',
            'manual_review_records' => 'integer',
            'duplicate_records' => 'integer',
            'invalid_records' => 'integer',
            'imported_records' => 'integer',
            'failed_records' => 'integer',
            'validated_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function stagingImports(): HasMany
    {
        return $this->hasMany(StagingCaseImport::class);
    }
}
