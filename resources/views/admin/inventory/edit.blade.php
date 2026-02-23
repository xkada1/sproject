@extends('layouts.app')

@section('title', 'Edit Inventory')

@section('content')
<div class="max-w-xl bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-2">Edit Inventory</h1>
    <p class="text-gray-600 mb-6">Product: <span class="font-semibold">{{ $product->name }}</span></p>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('inventory.update', $product->id) }}">
        @csrf
        @method('PATCH')

        <label class="block text-sm font-semibold mb-1">Stock</label>
        <input type="number"
               name="stock"
               min="0"
               value="{{ old('stock', $product->stock) }}"
               placeholder="Leave empty for Unlimited"
               class="w-full border p-2 rounded mb-2">

        <p class="text-xs text-gray-500 mb-4">
            Leave empty = Unlimited stock. Reserved stock will be handled automatically.
        </p>

        <div class="flex gap-2">
            <button class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
            <a href="{{ route('inventory.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Back</a>
        </div>
    </form>
</div>
@endsection