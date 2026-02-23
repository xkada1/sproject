<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    private function currentBranchId(): ?int
    {
        return Auth::user()?->branch_id;
    }

public function index()
{
    $query = Product::with(['category', 'supplier', 'branch']);

    if ($this->currentBranchId()) {
        $query->where('branch_id', $this->currentBranchId());
    }
    
    if (request('search')) {
        $query->where('name', 'like', '%' . request('search') . '%');
    }
    
    if (request('category_id')) {
        $query->where('category_id', request('category_id'));
    }
    
    $products = $query->get();
    return view('admin.products.index', compact('products'));
}

    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'suppliers', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'cost_price' => $request->cost_price ?? 0,
            'supplier_id' => $request->supplier_id,
            'branch_id' => $request->branch_id ?? $this->currentBranchId(),
            'description' => $request->description,
        ]);
        return redirect()->route('products.index')->with('success', 'Product created');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $suppliers = Supplier::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories', 'suppliers', 'branches'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'cost_price' => $request->cost_price ?? 0,
            'supplier_id' => $request->supplier_id,
            'branch_id' => $request->branch_id ?? $product->branch_id ?? $this->currentBranchId(),
            'description' => $request->description,
        ]);
        return redirect()->route('products.index')->with('success', 'Product updated');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted');
    }
}