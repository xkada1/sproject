@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex items-start justify-between gap-4 mb-4">
        <div>
            <h1 class="text-xl font-bold">Purchase Orders</h1>
            <p class="text-sm text-gray-500">Track supplier purchases per branch.</p>
        </div>
        <div class="flex gap-2">
            <form method="GET" class="flex gap-2">
                <input name="q" value="{{ $q }}" placeholder="Search PO..." class="border rounded p-2">
                <button class="bg-gray-900 text-white px-4 rounded">Search</button>
            </form>
            <a href="{{ route('purchase-orders.create') }}" class="bg-blue-600 text-white px-4 rounded flex items-center">+ New</a>
        </div>
    </div>

    <div class="bg-white rounded shadow overflow-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left">
                    <th class="p-3">PO #</th>
                    <th class="p-3">Supplier</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Updated</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseOrders as $po)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3"><a class="text-blue-700 underline" href="{{ route('purchase-orders.show', $po) }}">{{ $po->po_number }}</a></td>
                        <td class="p-3">{{ $po->supplier?->name }}</td>
                        <td class="p-3">{{ ucfirst($po->status) }}</td>
                        <td class="p-3">₱{{ number_format($po->total_amount, 2) }}</td>
                        <td class="p-3">{{ $po->updated_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr><td class="p-4 text-gray-500" colspan="5">No purchase orders</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $purchaseOrders->links() }}</div>
</div>
@endsection
