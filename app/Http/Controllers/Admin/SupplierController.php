<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $suppliers = Supplier::query()
            ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.suppliers.index', compact('suppliers', 'q'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        Supplier::create($data);

        return redirect()->route('suppliers.index')->with('success', 'Supplier added.');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $supplier->update($data);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted.');
    }
}
