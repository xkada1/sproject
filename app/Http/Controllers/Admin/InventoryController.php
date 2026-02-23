<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
{
    $q = $request->get('q');
    $low = $request->get('low'); // '1' = low stock only

    $products = Product::query()
        ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%"))
        ->when($low === '1', function ($query) {
            // Low = stock is not null AND available <= 5
            $query->whereNotNull('stock')
                ->whereRaw('(stock - reserved_stock) <= 5');
        })
        ->orderBy('name')
        ->paginate(15)
        ->withQueryString();

    return view('admin.inventory.index', compact('products', 'q', 'low'));
}
    public function edit(Product $product)
    {
        return view('admin.inventory.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            // stock can be NULL = unlimited
            'stock' => ['nullable', 'integer', 'min:0'],
        ]);

        // If user clears input, set to NULL (unlimited)
        $product->stock = $data['stock'] ?? null;

        // If stock is set, make sure reserved_stock does not exceed it
        if (!is_null($product->stock)) {
            $product->reserved_stock = min((int) $product->reserved_stock, (int) $product->stock);
        }

        $product->save();

        return redirect()->route('inventory.index')->with('success', 'Inventory updated.');
    }
}