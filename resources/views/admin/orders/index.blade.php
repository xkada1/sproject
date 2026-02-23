@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">All Orders</h1>
    
    <!-- Status Filter -->
    <form method="GET" class="flex gap-2">
        <select name="status" onchange="this.form.submit()" class="border p-2 rounded">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </form>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-200">
            <tr class="text-left">
                <th class="p-3">Order ID</th>
                <th class="p-3">Table</th>
                <th class="p-3">Cashier</th>
                <th class="p-3">Total</th>
                <th class="p-3">Status</th>
                <th class="p-3">Date</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3">#{{ $order->id }}</td>
                <td class="p-3">{{ $order->table->name ?? 'N/A' }}</td>
                <td class="p-3">{{ $order->user->name ?? 'N/A' }}</td>
                <td class="p-3 font-bold">₱{{ number_format($order->total_amount, 2) }}</td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded text-sm font-semibold
                        {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' : 
                           ($order->status == 'cancelled' ? 'bg-red-100 text-red-800' : 
                           ($order->status == 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td class="p-3 text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</td>
                <td class="p-3">
                    <a href="{{ route('orders.show', $order->id) }}" class="text-blue-600 hover:underline mr-3">View</a>
                    
                    @if($order->status == 'pending')
                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="inline mr-2">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="processing">
                        <button class="text-blue-600 hover:underline mr-2">Process</button>
                    </form>
                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="inline mr-2">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button class="text-green-600 hover:underline mr-2">Complete</button>
                    </form>
                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button class="text-red-600 hover:underline" onclick="return confirm('Cancel this order?')">Cancel</button>
                    </form>
                    @endif
                    
                    @if($order->status == 'processing')
                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="inline mr-2">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button class="text-green-600 hover:underline mr-2">Complete</button>
                    </form>
                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button class="text-red-600 hover:underline" onclick="return confirm('Cancel this order?')">Cancel</button>
                    </form>
                    @endif
                    
                    @if(in_array($order->status, ['completed', 'cancelled']))
                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline" onclick="return confirm('Delete this order permanently?')">Delete</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="p-6 text-center text-gray-500">No orders found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection