@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="mb-4">
        <h1 class="text-xl font-bold">New Purchase Order</h1>
        <p class="text-sm text-gray-500">Add items (name, qty, unit cost). You can edit status later.</p>
    </div>

    <form method="POST" action="{{ route('purchase-orders.store') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Supplier</label>
                <select name="supplier_id" class="border rounded p-2 w-full" required>
                    <option value="">Select supplier...</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">PO Number (optional)</label>
                <input name="po_number" class="border rounded p-2 w-full" placeholder="PO-0001">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Notes (optional)</label>
            <textarea name="notes" class="border rounded p-2 w-full" rows="2"></textarea>
        </div>

        <div class="border rounded p-4">
            <div class="font-semibold mb-2">Items</div>
            <p class="text-xs text-gray-500 mb-3">Add at least 1 line. Leave blank lines empty.</p>

            <div class="space-y-2">
                @for($i=0;$i<8;$i++)
                    <div class="grid grid-cols-12 gap-2">
                        <input name="items[{{ $i }}][product_name]" class="border rounded p-2 col-span-6" placeholder="Item name">
                        <input name="items[{{ $i }}][quantity]" type="number" min="0" class="border rounded p-2 col-span-2" placeholder="Qty">
                        <input name="items[{{ $i }}][unit_cost]" type="number" step="0.01" min="0" class="border rounded p-2 col-span-4" placeholder="Unit cost">
                    </div>
                @endfor
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('purchase-orders.index') }}" class="px-4 py-2 rounded border">Cancel</a>
            <button class="px-4 py-2 rounded bg-gray-900 text-white">Save PO</button>
        </div>
    </form>
</div>
@endsection
