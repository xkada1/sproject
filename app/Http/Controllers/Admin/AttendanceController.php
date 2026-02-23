<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role ?? 'cashier';
        $branchId = session('branch_id');

        $q = $request->get('q');

        $query = Attendance::query()
            ->with(['user', 'branch'])
            ->when($branchId, fn ($qq) => $qq->where('branch_id', $branchId))
            ->when($q, function ($qq) use ($q) {
                $qq->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%"));
            })
            ->orderByDesc('work_date')
            ->orderByDesc('id');

        // Cashier sees only their own records
        if (!in_array($role, ['admin', 'manager'], true)) {
            $query->where('user_id', $user->id);
        }

        $rows = $query->paginate(15)->withQueryString();

        $today = now()->toDateString();
        $todayRow = Attendance::query()
            ->where('branch_id', $branchId)
            ->where('user_id', $user->id)
            ->whereDate('work_date', $today)
            ->first();

        return view('admin.attendance.index', [
            'rows' => $rows,
            'todayRow' => $todayRow,
            'q' => $q,
            'role' => $role,
        ]);
    }

    public function clockIn(Request $request)
    {
        $user = Auth::user();
        $branchId = session('branch_id');
        $today = now()->toDateString();

        $row = Attendance::firstOrCreate(
            ['branch_id' => $branchId, 'user_id' => $user->id, 'work_date' => $today],
            ['clock_in' => null, 'clock_out' => null]
        );

        if ($row->clock_in) {
            return back()->with('error', 'You are already clocked in today.');
        }

        $row->clock_in = now();
        $row->save();

        return back()->with('success', 'Clock-in recorded.');
    }

    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $branchId = session('branch_id');
        $today = now()->toDateString();

        $row = Attendance::query()
            ->where('branch_id', $branchId)
            ->where('user_id', $user->id)
            ->whereDate('work_date', $today)
            ->first();

        if (!$row || !$row->clock_in) {
            return back()->with('error', 'Clock-in first before clock-out.');
        }

        if ($row->clock_out) {
            return back()->with('error', 'You are already clocked out today.');
        }

        $row->clock_out = now();
        $row->save();

        return back()->with('success', 'Clock-out recorded.');
    }
}
