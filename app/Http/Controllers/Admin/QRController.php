<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QRController extends Controller
{
    public function index(Request $request)
    {
        $branchId = Auth::user()?->branch_id;

        $q = $request->get('q');

        $tables = Table::query()
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.qr.index', compact('tables', 'q'));
    }

    public function regenerate(Table $table)
    {
        $table->qr_token = Str::random(32);
        $table->save();

        return back()->with('success', 'QR token regenerated.');
    }
}
