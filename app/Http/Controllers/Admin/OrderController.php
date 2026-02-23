<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    private function currentBranchId(): int
    {
        // Franchise-ready default: use user's branch_id, else fallback to 1
        return (int) (Auth::user()->branch_id ?? 1);
    }

    public function pos()
    {
        $branchId = $this->currentBranchId();

        $tables = Table::query()
            ->where('branch_id', $branchId)
            ->where('status', 'available')
            ->get();

        $categories = \App\Models\Category::with(['products' => function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])->get();

        return view('admin.orders.pos', compact('tables', 'categories'));
    }

    public function storeOrder(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:dine-in,takeout,delivery',
            'table_id' => 'nullable|exists:tables,id',
            'notes' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',

            // payment fields
            'payment_method' => 'nullable|in:cash,gcash,card',
            'amount_tendered' => 'nullable|numeric|min:0',
        ]);

        if ($request->order_type === 'dine-in' && !$request->table_id) {
            return back()->withErrors(['table_id' => 'Table is required for dine-in.'])->withInput();
        }

        $branchId = $this->currentBranchId();

        // Normalize items by product_id (merge duplicates)
        $merged = [];
        foreach ($request->items as $row) {
            $pid = (int) $row['id'];
            $qty = (int) $row['qty'];
            if (!isset($merged[$pid])) $merged[$pid] = 0;
            $merged[$pid] += $qty;
        }

        $items = [];
        foreach ($merged as $pid => $qty) {
            $items[] = ['id' => $pid, 'qty' => $qty];
        }

        $subtotal = 0.0;
        $discountPercent = (float) ($request->discount ?? 0);
        $paymentMethod = $request->payment_method;
        $amountTendered = $request->amount_tendered !== null ? (float) $request->amount_tendered : null;

        try {
            $order = DB::transaction(function () use ($request, $items, &$subtotal, $discountPercent, $paymentMethod, $amountTendered, $branchId) {
                // 1) Lock products + validate stock availability
                $products = [];
                foreach ($items as $row) {
                    $product = Product::where('id', $row['id'])
                        ->where('branch_id', $branchId)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $qty = (int) $row['qty'];
                    $price = (float) $product->price;
                    $subtotal += $price * $qty;

                    // If stock is NULL => unlimited stock
                    if (!is_null($product->stock)) {
                        $available = (int) $product->stock - (int) $product->reserved_stock;
                        if ($available < $qty) {
                            throw new \RuntimeException("Not enough stock for {$product->name}. Available: {$available}");
                        }
                    }

                    $products[$product->id] = $product;
                }

                $discountAmount = $subtotal * ($discountPercent / 100);
                $total = max(0, $subtotal - $discountAmount);

                // 2) Validate payment if cash
                $change = null;
                $paidAt = null;
                $finalTendered = $amountTendered;

                if ($paymentMethod) {
                    if ($paymentMethod === 'cash') {
                        if ($finalTendered === null || $finalTendered < $total) {
                            throw new \RuntimeException('Cash received must be greater than or equal to total.');
                        }
                        $change = $finalTendered - $total;
                    } else {
                        $finalTendered = null;
                        $change = 0.0;
                    }
                    $paidAt = now();
                }

                // 3) Create order (PENDING)
                $order = Order::create([
                    'branch_id' => $branchId,
                    'table_id' => $request->order_type === 'dine-in' ? $request->table_id : null,
                    'user_id' => Auth::id(),
                    'total_amount' => $total,
                    'status' => 'pending',
                    'order_type' => $request->order_type,
                    'notes' => $request->notes,
                    'discount' => $discountPercent,

                    'payment_method' => $paymentMethod,
                    'amount_tendered' => $finalTendered,
                    'change_amount' => $change,
                    'paid_at' => $paidAt,
                ]);

                // 4) Create items + RESERVE stock (do NOT deduct yet)
                foreach ($items as $row) {
                    $product = $products[$row['id']];
                    $qty = (int) $row['qty'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'price' => (float) $product->price,
                    ]);

                    // If stock is NULL => unlimited, skip
                    if (!is_null($product->stock)) {
                        $beforeStock = (int) $product->stock;
                        $beforeReserved = (int) $product->reserved_stock;

                        $product->reserved_stock = $beforeReserved + $qty;
                        $product->save();

                        InventoryLog::create([
                            'product_id' => $product->id,
                            'user_id' => Auth::id(),
                            'action' => 'reserve',
                            'qty' => $qty,
                            'stock_before' => $beforeStock,
                            'stock_after' => (int) $product->stock,
                            'reserved_before' => $beforeReserved,
                            'reserved_after' => (int) $product->reserved_stock,
                            'reference' => 'order#' . $order->id,
                        ]);
                    }
                }

                // 5) Mark table occupied for dine-in
                if ($request->order_type === 'dine-in' && $request->table_id) {
                    Table::where('id', $request->table_id)
                        ->where('branch_id', $branchId)
                        ->update(['status' => 'occupied']);
                }

                return $order;
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('orders.print', $order->id);
    }

    public function index(Request $request)
    {
        $branchId = $this->currentBranchId();

        $query = Order::with(['table', 'user'])
            ->where('branch_id', $branchId)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('orderItems.product', 'table', 'user');
        return view('admin.orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        DB::transaction(function () use ($order) {
            $order->load('orderItems.product');

            // If not completed, release reservation
            if ($order->status !== 'completed') {
                foreach ($order->orderItems as $item) {
                    $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                    if ($product && !is_null($product->stock)) {
                        $qty = (int) $item->quantity;

                        $beforeStock = (int) $product->stock;
                        $beforeReserved = (int) $product->reserved_stock;

                        $product->reserved_stock = max(0, $beforeReserved - $qty);
                        $product->save();

                        InventoryLog::create([
                            'product_id' => $product->id,
                            'user_id' => Auth::id(),
                            'action' => 'unreserve',
                            'qty' => $qty,
                            'stock_before' => $beforeStock,
                            'stock_after' => (int) $product->stock,
                            'reserved_before' => $beforeReserved,
                            'reserved_after' => (int) $product->reserved_stock,
                            'reference' => 'order#' . $order->id,
                        ]);
                    }
                }
            }

            $order->orderItems()->delete();
            $order->delete();
        });

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,held',
        ]);

        $newStatus = $request->status;
        $oldStatus = $order->status;

        if ($oldStatus !== $newStatus) {
            try {
                DB::transaction(function () use ($order, $newStatus) {
                    $order->load('orderItems.product');

                    if ($newStatus === 'completed') {
                        foreach ($order->orderItems as $oi) {
                            $product = Product::where('id', $oi->product_id)->lockForUpdate()->first();
                            if (!$product) throw new \RuntimeException('Product not found for one of the order items.');

                            $qty = (int) $oi->quantity;
                            if (is_null($product->stock)) {
                                continue; // unlimited
                            }

                            $beforeStock = (int) $product->stock;
                            $beforeReserved = (int) $product->reserved_stock;

                            if ($beforeStock < $qty) {
                                throw new \RuntimeException("Not enough stock for {$product->name}. Stock: {$beforeStock}");
                            }

                            $product->stock = max(0, $beforeStock - $qty);
                            $product->reserved_stock = max(0, $beforeReserved - $qty);
                            $product->save();

                            InventoryLog::create([
                                'product_id' => $product->id,
                                'user_id' => Auth::id(),
                                'action' => 'deduct',
                                'qty' => $qty,
                                'stock_before' => $beforeStock,
                                'stock_after' => (int) $product->stock,
                                'reserved_before' => $beforeReserved,
                                'reserved_after' => (int) $product->reserved_stock,
                                'reference' => 'order#' . $order->id,
                            ]);
                        }
                    }

                    if ($newStatus === 'cancelled') {
                        foreach ($order->orderItems as $oi) {
                            $product = Product::where('id', $oi->product_id)->lockForUpdate()->first();
                            if (!$product) continue;

                            $qty = (int) $oi->quantity;
                            if (is_null($product->stock)) {
                                continue; // unlimited
                            }

                            $beforeStock = (int) $product->stock;
                            $beforeReserved = (int) $product->reserved_stock;

                            $product->reserved_stock = max(0, $beforeReserved - $qty);
                            $product->save();

                            InventoryLog::create([
                                'product_id' => $product->id,
                                'user_id' => Auth::id(),
                                'action' => 'unreserve',
                                'qty' => $qty,
                                'stock_before' => $beforeStock,
                                'stock_after' => (int) $product->stock,
                                'reserved_before' => $beforeReserved,
                                'reserved_after' => (int) $product->reserved_stock,
                                'reference' => 'order#' . $order->id,
                            ]);
                        }
                    }
                });
            } catch (\Throwable $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        $order->update(['status' => $newStatus]);

        // Free table if completed/cancelled
        if (in_array($newStatus, ['completed', 'cancelled'], true) && $order->table_id) {
            $order->table()->update(['status' => 'available']);
        }

        return redirect()->route('orders.index')->with('success', 'Order status updated');
    }

    public function printReceipt(Order $order)
    {
        $order->load('orderItems.product', 'table', 'user');

        if (view()->exists('admin.orders.receipt')) {
            return view('admin.orders.receipt', compact('order'));
        }

        if (view()->exists('admin.orders.receipts')) {
            return view('admin.orders.receipts', compact('order'));
        }

        abort(500, 'Receipt view not found. Create: resources/views/admin/orders/receipt.blade.php');
    }
}
