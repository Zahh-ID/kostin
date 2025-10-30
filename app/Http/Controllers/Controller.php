<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;

    protected function recordAudit(string $action, string $entity, int $entityId, array $meta = []): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'meta_json' => $meta,
            'created_at' => now(),
        ]);
    }
}
