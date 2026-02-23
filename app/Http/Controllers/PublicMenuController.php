<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicMenuController extends Controller
{
    public function menu(string $token)
    {
        $table = Table::where('qr_token', $token)->firstOrFail();

        $categories = Category::with(['products' => function ($q) use ($table) {
            $q->where(function ($qq) use ($table) {
                // Show products for the table's branch, or global (null)
                $qq->whereNull('branch_id')->orWhere('branch_id', $table->branch_id);
            })->orderBy('name');
        }])->orderBy('name')->get();

        return view('public.menu', compact('table', 'categories'));
    }

    public function place(Request $request, string $token)
    {
        $table = Table::where('qr_token', $token)->firstOrFail();

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'customer_name' => 'nullable|string|max:80',
        ]);

        // Merge duplicates
        $merged = [];
        foreach ($request->items as $row) {
            $pid = (int) $row['id'];
            $qty = (int) $row['qty'];
            $merged[$pid] = ($merged[$pid] ?? 0) + $qty;
        }

        $items = [];
        foreach ($merged as $pid => $qty) {
            $items[] = ['id' => $pid, 'qty' => $qty];
        }

        $order = DB::transaction(function () use ($table, $items, $request) {
            $subtotal = 0.0;
            $products = [];

            foreach ($items as $row) {
                $product = Product::where('id', $row['id'])->lockForUpdate()->firstOrFail();
                $qty = (int) $row['qty'];
                $subtotal += (float) $product->price * $qty;
                $products[$product->id] = $product;
            }

            $order = Order::create([
                'branch_id' => $table->branch_id,
                'table_id' => $table->id,
                'user_id' => null,
                'total_amount' => $subtotal,
                'status' => 'pending',
                'order_type' => 'dine-in',
                'notes' => $request->notes,
                'discount' => 0,
                // Optional: store the name inside notes if you don't have a column yet
                // (keeps this feature migration-free)
            ]);

            foreach ($items as $row) {
                $product = $products[$row['id']];
                $qty = (int) $row['qty'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => (float) $product->price,
                ]);
            }

            // Mark table occupied
            $table->update(['status' => 'occupied']);

            return $order;
        });

        return redirect()
            ->route('menu.track', ['token' => $token, 'order' => $order->id])
            ->with('success', 'Order sent! You can track the status here.');
    }

    public function track(string $token, Order $order)
    {
        $table = Table::where('qr_token', $token)->firstOrFail();

        // Safety: only allow viewing orders for this table
        abort_unless((int) $order->table_id === (int) $table->id, 404);

        $order->load(['orderItems.product']);

        return view('public.track', compact('table', 'order'));
    }
}
