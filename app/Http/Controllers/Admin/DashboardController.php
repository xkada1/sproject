<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Table;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
{
    $todayOrders = \App\Models\Order::whereDate('created_at', today())->count();

    $todayRevenue = \App\Models\Order::whereDate('created_at', today())
        ->where('status', 'completed')
        ->sum('total_amount');

    $totalTables = \App\Models\Table::count();
    $totalProducts = \App\Models\Product::count();

    $recentOrders = \App\Models\Order::with(['table', 'user'])
        ->latest()
        ->take(10)
        ->get();

    // 🔴 Low Stock Count
    $lowStockCount = \App\Models\Product::whereNotNull('stock')
        ->whereRaw('(stock - reserved_stock) <= 5')
        ->count();

    // 🟢 Daily Completed Orders Count
    $todayCompleted = \App\Models\Order::whereDate('created_at', today())
        ->where('status', 'completed')
        ->count();

        $dailySales = \App\Models\Order::selectRaw('DATE(created_at) as day, COUNT(*) as orders, SUM(total_amount) as revenue')
    ->where('status', 'completed')
    ->groupBy('day')
    ->orderBy('day', 'desc')
    ->take(7)
    ->get();

    return view('dashboard', compact(
    'todayOrders',
    'todayRevenue',
    'totalTables',
    'totalProducts',
    'recentOrders',
    'lowStockCount',
    'todayCompleted',
    'dailySales'
));
}
}