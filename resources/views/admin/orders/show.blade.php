@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
    <div class="text-center border-b pb-4 mb-4">
        <h1 class="text-2xl font-bold">Saucy Wing</h1>
        <p class="text-gray-500">Order #{{ $order->id }}</p>
        <p class="text-gray-500">Table: {{ $order->table->name }}</p>
        <p class="text-gray-500">Cashier: {{ $order->user->name }}</p>
        <p class="text-gray-500">Date: {{ $order->created_at->format('M d, Y H:i') }}</p>
    </div>

    <table class="w-full mb-4">
        <thead>
            <tr class="border-b">
                <th class="text-left py-2">Item</th>
                <th class="text-center py-2">Qty</th>
                <th class="text-right py-2">Price</th>
                <th class="text-right py-2">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
            <tr class="border-b">
                <td class="py-2">{{ $item->product->name }}</td>
                <td class="text-center py-2">{{ $item->quantity }}</td>
                <td class="text-right py-2">₱{{ number_format($item->price, 2) }}</td>
                <td class="text-right py-2">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-right text-xl font-bold">
        Total: ₱{{ number_format($order->total_amount, 2) }}
    </div>

    <div class="mt-6 flex gap-2 justify-center">
        @if($order->status == 'pending')
        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="completed">
            <button class="bg-green-600 text-white px-4 py-2 rounded">Complete Order</button>
        </form>
        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="cancelled">
            <button class="bg-red-600 text-white px-4 py-2 rounded">Cancel Order</button>
        </form>
        @endif
        <a href="{{ route('orders.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Back</a>
    </div>
</div>
@endsection