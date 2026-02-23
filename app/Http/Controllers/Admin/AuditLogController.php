<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $branchId = session('branch_id');

        $logs = AuditLog::query()
            ->when($branchId, fn ($qr) => $qr->where('branch_id', $branchId))
            ->when($q, function ($qr) use ($q) {
                $qr->where('action', 'like', "%{$q}%")
                    ->orWhere('entity_type', 'like', "%{$q}%");
            })
            ->with('user')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.audit.index', compact('logs', 'q'));
    }
}
