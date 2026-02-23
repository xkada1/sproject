<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetupController extends Controller
{
    public function createBranch()
    {
        return view('admin.setup.branch');
    }

    public function storeBranch(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $branch = DB::transaction(function () use ($data) {
            return Branch::create($data);
        });

        session(['branch_id' => $branch->id]);

        return redirect()->route('dashboard')->with('success', 'Branch created.');
    }
}
