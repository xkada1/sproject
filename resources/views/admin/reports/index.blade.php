@extends('layouts.app')

@section('title','Reports')

@section('content')
<div class="bg-white rounded shadow p-4 space-y-4">
    <h2 class="text-xl font-bold">Sales Reports</h2>

    <form class="flex gap-2 items-end" method="GET">
        <div>
            <label class="text-sm">From</label>
            <input type="date" name="from" value="{{ $from }}" class="border p-2 rounded">
        </div>
        <div>
            <label class="text-sm">To</label>
            <input type="date" name="to" value="{{ $to }}" class="border p-2 rounded">
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>

        <a class="ml-auto bg-green-600 text-white px-4 py-2 rounded"
           href="{{ route('reports.export.csv', ['from'=>$from,'to'=>$to]) }}">
           Export CSV
        </a>
    </form>

    <div class="grid grid-cols-2 gap-4">
        <div class="p-3 border rounded">
            <div class="text-sm text-gray-500">Completed Sales</div>
            <div class="text-2xl font-bold">₱{{ number_format($sales,2) }}</div>
        </div>
        <div class="p-3 border rounded">
            <div class="text-sm text-gray-500">Orders</div>
            <div class="text-2xl font-bold">{{ $count }}</div>
        </div>
    </div>

    <table class="w-full">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 text-left">#</th>
                <th class="p-2 text-left">Date</th>
                <th class="p-2 text-left">Type</th>
                <th class="p-2 text-left">Table</th>
                <th class="p-2 text-left">Cashier</th>
                <th class="p-2 text-left">Total</th>
                <th class="p-2 text-left">Status</th>
                <th class="p-2 text-left">Payment</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $o)
            <tr class="border-b">
                <td class="p-2">#{{ $o->id }}</td>
                <td class="p-2">{{ $o->created_at->format('Y-m-d H:i') }}</td>
                <td class="p-2">{{ ucfirst($o->order_type) }}</td>
                <td class="p-2">{{ $o->table->name ?? '—' }}</td>
                <td class="p-2">{{ $o->user->name ?? '—' }}</td>
                <td class="p-2 font-bold">₱{{ number_format($o->total_amount,2) }}</td>
                <td class="p-2">{{ ucfirst($o->status) }}</td>
                <td class="p-2">{{ strtoupper($o->payment_method ?? '—') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection