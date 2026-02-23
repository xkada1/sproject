@extends('layouts.app')

@section('title','Held Orders')

@section('content')
<div class="bg-white rounded shadow p-4">
    <h2 class="text-xl font-bold mb-4">Held Orders</h2>

    <table class="w-full">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 text-left">#</th>
                <th class="p-2 text-left">Type</th>
                <th class="p-2 text-left">Table</th>
                <th class="p-2 text-left">Total</th>
                <th class="p-2 text-left">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $o)
            <tr class="border-b">
                <td class="p-2">#{{ $o->id }}</td>
                <td class="p-2">{{ ucfirst($o->order_type) }}</td>
                <td class="p-2">{{ $o->table->name ?? '—' }}</td>
                <td class="p-2 font-bold">₱{{ number_format($o->total_amount,2) }}</td>
                <td class="p-2">
                    <form method="POST" action="{{ route('orders.resume',$o->id) }}">
                        @csrf
                        <button class="px-3 py-1 bg-blue-600 text-white rounded">Resume</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection