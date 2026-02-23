@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="bg-white rounded-lg shadow p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Inventory</h1>

        <form method="GET" class="flex gap-2 items-center">
            <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search product..."
                   class="border p-2 rounded w-64">

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="low" value="1" {{ ($low ?? '') === '1' ? 'checked' : '' }}>
                Low stock only
            </label>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">Apply</button>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr class="text-left">
                    <th class="p-3">Product</th>
                    <th class="p-3">Stock</th>
                    <th class="p-3">Reserved</th>
                    <th class="p-3">Available</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    @php
                        $stock = $product->stock; // NULL = unlimited
                        $reserved = (int) $product->reserved_stock;
                        $availableNum = is_null($stock) ? null : max(0, (int)$stock - $reserved);
                        $isLow = (!is_null($availableNum) && $availableNum <= 5);
                    @endphp

                    <tr class="border-b {{ $isLow ? 'bg-red-50' : '' }}">
                        <td class="p-3 font-medium">
                            {{ $product->name }}
                            @if($isLow)
                                <span class="ml-2 text-xs font-bold text-red-600">LOW</span>
                            @endif
                        </td>

                        <td class="p-3">{{ is_null($stock) ? 'Unlimited' : $stock }}</td>
                        <td class="p-3">{{ $reserved }}</td>
                        <td class="p-3">
                            {{ is_null($availableNum) ? 'Unlimited' : $availableNum }}
                        </td>

                        <td class="p-3">
                            <a href="{{ route('inventory.edit', $product->id) }}" class="text-blue-600 hover:underline">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection