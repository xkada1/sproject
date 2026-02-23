@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <div class="flex items-start justify-between gap-4 mb-4">
        <div>
            <h1 class="text-xl font-bold">PO {{ $po->po_number }}</h1>
            <p class="text-sm text-gray-500">Supplier: {{ $po->supplier?->name ?? '—' }} • Status: {{ ucfirst($po->status) }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('purchase-orders.index') }}" class="border px-3 py-2 rounded">Back</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-200 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="border rounded p-4">
            <div class="text-xs text-gray-500">Branch</div>
            <div class="font-bold">{{ $po->branch?->name ?? '—' }}</div>
        </div>
        <div class="border rounded p-4">
            <div class="text-xs text-gray-500">Total</div>
            <div class="font-bold">₱{{ number_format($po->total_amount,2) }}</div>
        </div>
        <div class="border rounded p-4">
            <div class="text-xs text-gray-500">Ordered / Received</div>
            <div class="font-bold">{{ $po->ordered_at?->format('Y-m-d') ?? '—' }} / {{ $po->received_at?->format('Y-m-d') ?? '—' }}</div>
        </div>
    </div>

    <div class="border rounded p-4 mb-4">
        <div class="font-bold mb-2">Items</div>
        <div class="overflow-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-left">
                        <th class="py-2">Product</th>
                        <th>Qty</th>
                        <th>Unit Cost</th>
                        <th>Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($po->items as $it)
                        <tr class="border-b">
                            <td class="py-2">{{ $it->product_name }}</td>
                            <td>{{ (int)$it->quantity }}</td>
                            <td>₱{{ number_format($it->unit_cost,2) }}</td>
                            <td>₱{{ number_format($it->line_total,2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="border rounded p-4">
        <div class="font-bold mb-2">Update Status</div>
        <form method="POST" action="{{ route('purchase-orders.status', $po) }}" class="flex flex-wrap gap-2 items-end">
            @csrf
            <div>
                <label class="text-xs text-gray-500">Status</label>
                <select name="status" class="border rounded p-2">
                    @foreach(['draft','ordered','received','cancelled'] as $st)
                        <option value="{{ $st }}" @selected($po->status === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <button class="bg-gray-900 text-white px-4 py-2 rounded">Save</button>
        </form>
    </div>
</div>
@endsection
