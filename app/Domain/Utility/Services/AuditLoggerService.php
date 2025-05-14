<?php

declare(strict_types=1);

namespace App\Domain\Utility\Services;

use App\Domain\Utility\Models\AuditLog;
use App\Domain\UserManagement\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request; // For IP and User Agent

class AuditLoggerService
{
    public function logAction(
        string $action,
        Model $auditable = null,
        User $user = null,
        array $oldValues = null,
        array $newValues = null,
        string $details = null,
        Request $request = null
    ): void {
        AuditLog::create([
            'user_id' => $user?->id ?? auth()->id(),
            'action' => $action,
            'auditable_type' => $auditable ? $auditable->getMorphClass() : null,
            'auditable_id' => $auditable?->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'details' => $details,
        ]);
    }
}
