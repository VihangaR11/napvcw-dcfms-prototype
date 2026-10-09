<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    public function record(
        string $action,
        ?Model $auditable = null,
        array $oldValues = [],
        array $newValues = [],
        ?string $description = null,
        ?string $module = null,
        ?int $caseId = null
    ): AuditLog {
        $request = request();

        return AuditLog::create([
            'user_id' =>
                auth()->id(),

            'action' =>
                $action,

            'auditable_type' =>
                $auditable
                    ? get_class($auditable)
                    : null,

            'auditable_id' =>
                $auditable?->getKey(),

            'dcfms_case_id' =>
                $caseId,

            'module' =>
                $module,

            'old_values' =>
                !empty($oldValues)
                    ? $oldValues
                    : null,

            'new_values' =>
                !empty($newValues)
                    ? $newValues
                    : null,

            'description' =>
                $description,

            'ip_address' =>
                $request?->ip(),

            'user_agent' =>
                $request?->userAgent(),
        ]);
    }
}