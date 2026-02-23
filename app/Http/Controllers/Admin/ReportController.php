<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function profit(Request $request)
    {
        $branchId = $request->integer('branch_id') ?: (Auth::user()->branch_id ?: 1);
        $from = $request->get('from') ?: now()->startOfMonth()->toDateString();
        $to = $request->get('to') ?: now()->toDateString();

        $orders = Order::query()
            ->where('status', 'completed')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->where('branch_id', $branchId)
            ->with('orderItems.product')
            ->get();

        $revenue = (float) $orders->sum('total_amount');
        $cogs = 0.0;
        foreach ($orders as $o) {
            foreach ($o->orderItems as $oi) {
                $cost = (float) ($oi->product?->cost_price ?? 0);
                $cogs += $cost * (int) $oi->quantity;
            }
        }

        // Expenses table uses `spent_on` (DATE). If you migrated earlier versions,
        // this keeps profit report working.
        $expensesTotal = (float) Expense::query()
            ->where('branch_id', $branchId)
            ->whereBetween('spent_on', [$from, $to])
            ->sum('amount');

        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $expensesTotal;

        // Top products by profit
        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->selectRaw('products.name as name, SUM(order_items.quantity) as qty, SUM((order_items.price - COALESCE(products.cost_price,0)) * order_items.quantity) as profit')
            ->where('orders.status', 'completed')
            ->where('orders.branch_id', $branchId)
            ->whereBetween('orders.created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->groupBy('products.name')
            ->orderByDesc('profit')
            ->limit(10)
            ->get();

        // Daily summary (revenue + gross profit)
        $daily = DB::table('orders')
            ->leftJoin('order_items', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.status', 'completed')
            ->where('orders.branch_id', $branchId)
            ->whereBetween('orders.created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->groupByRaw('DATE(orders.created_at)')
            ->orderByRaw('DATE(orders.created_at) DESC')
            ->selectRaw('DATE(orders.created_at) as day')
            ->selectRaw('COUNT(DISTINCT orders.id) as orders')
            ->selectRaw('SUM(DISTINCT orders.total_amount) as revenue')
            ->selectRaw('SUM((order_items.price - COALESCE(products.cost_price,0)) * order_items.quantity) as gross_profit')
            ->limit(31)
            ->get();

        $branches = Branch::orderBy('name')->get();

        return view('admin.reports.profit', compact(
            'branches', 'branchId', 'from', 'to',
            'revenue', 'cogs', 'grossProfit', 'expensesTotal', 'netProfit',
            'topProducts', 'daily'
        ));
    }
}
