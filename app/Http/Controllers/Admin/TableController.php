<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TableController extends Controller
{
    private function currentBranchId(): ?int
    {
        return Auth::user()?->branch_id;
    }
    public function index()
    {
        $branchId = $this->currentBranchId();
        $tables = Table::query()
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderBy('name')
            ->get();
        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'capacity' => 'required|integer'
        ]);

        Table::create([
            'branch_id' => $this->currentBranchId(),
            'name' => $request->name,
            'capacity' => $request->capacity,
            'status' => 'available',
            'qr_token' => (string) Str::uuid(),
        ]);
        return redirect()->route('tables.index')->with('success', 'Table created successfully');
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'name' => 'required',
            'capacity' => 'required|integer'
        ]);

        $table->update([
            'name' => $request->name,
            'capacity' => $request->capacity,
        ]);
        return redirect()->route('tables.index')->with('success', 'Table updated successfully');
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('tables.index')->with('success', 'Table deleted successfully');
    }
}