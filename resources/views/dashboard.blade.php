@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-500">Today's Orders</h3>
        <p class="text-3xl font-bold text-blue-600">{{ $todayOrders }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-500">Today's Revenue</h3>
        <p class="text-3xl font-bold text-green-600">₱{{ number_format($todayRevenue, 2) }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-500">Total Tables</h3>
        <p class="text-3xl font-bold text-purple-600">{{ $totalTables }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-500">Total Products</h3>
        <p class="text-3xl font-bold text-orange-600">{{ $totalProducts }}</p>
    </div>
    <div class="bg-red-100 p-4 rounded shadow">
    <h3 class="text-lg font-bold text-red-700">Low Stock Items</h3>
    <p class="text-2xl font-bold">{{ $lowStockCount }}</p>
</div>
<div class="bg-green-100 p-4 rounded shadow">
    <h3 class="font-bold">Today's Sales</h3>
    <p>Orders: {{ $todayCompleted }}</p>
    <p>Revenue: ₱{{ number_format($todayRevenue,2) }}</p>
</div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-bold mb-4">Recent Orders</h2>
    <table class="w-full">
        <thead>
            <tr class="border-b">
                <th class="py-2">Order ID</th>
                <th>Table</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentOrders as $order)
            <tr class="border-b">
                <td class="py-2">#{{ $order->id }}</td>
                <td>
    @if($order->order_type === 'dine-in' && $order->table)
        {{ $order->table->name }}
    @elseif($order->order_type === 'takeout')
        Takeout
    @elseif($order->order_type === 'delivery')
        Delivery
    @else
        —
    @endif
</td>
                <td>₱{{ number_format($order->total_amount, 2) }}</td>
                <td>
                    <span class="px-2 py-1 rounded text-sm {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td>{{ $order->created_at->format('M d, H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<canvas id="salesChart" height="100"></canvas>

<script>
const ctx = document.getElementById('salesChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($dailySales->pluck('date')) !!},
        datasets: [{
            label: 'Sales ₱',
            data: {!! json_encode($dailySales->pluck('total')) !!},
            borderWidth: 2,
            fill: false
        }]
    }
});
</script>
@endsection