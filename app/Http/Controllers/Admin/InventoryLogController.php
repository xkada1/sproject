<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;

class InventoryLogController extends Controller
{
    public function index()
    {
        $logs = InventoryLog::with('product')
            ->latest()
            ->paginate(20);

        return view('admin.inventory.logs', compact('logs'));
    }
    public function productLogs(\App\Models\Product $product)
{
    $logs = InventoryLog::where('product_id', $product->id)
        ->latest()
        ->paginate(20);

    return view('admin.inventory.product_logs', compact('logs', 'product'));
}
}