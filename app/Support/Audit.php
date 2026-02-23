<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Audit
{
    public static function log(string $action, ?string $entityType = null, ?int $entityId = null, array $meta = [], ?Request $request = null): void
    {
        try {
            $user = Auth::user();
            $req = $request ?? request();

            AuditLog::create([
                'branch_id' => session('branch_id') ?? ($user->branch_id ?? null),
                'user_id' => $user?->id,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'meta' => $meta,
                'ip_address' => $req?->ip(),
                'user_agent' => substr((string)($req?->userAgent()), 0, 500),
            ]);
        } catch (\Throwable $e) {
            // Don't break primary flow if logging fails
        }
    }
}
