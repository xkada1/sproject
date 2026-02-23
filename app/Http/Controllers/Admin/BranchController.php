<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $branches = Branch::query()
            ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%")->orWhere('code', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.branches.index', compact('branches', 'q'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        Branch::create($data);

        return redirect()->route('branches.index')->with('success', 'Branch created.');
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $branch->update($data);

        return redirect()->route('branches.index')->with('success', 'Branch updated.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Branch deleted.');
    }

    public function switch(Request $request)
    {
        $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        session(['branch_id' => (int) $request->branch_id]);

        return back()->with('success', 'Branch switched.');
    }

}
