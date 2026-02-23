@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">Add New Product</h1>

<form action="{{ route('products.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-lg">
    @csrf
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Product Name</label>
        <input type="text" name="name" class="w-full border p-2 rounded" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Category</label>
        <select name="category_id" class="w-full border p-2 rounded" required>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Price</label>
        <input type="number" step="0.01" name="price" class="w-full border p-2 rounded" required>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Cost Price (for Profit Tracking)</label>
        <input type="number" step="0.01" name="cost_price" class="w-full border p-2 rounded" value="0">
        <p class="text-xs text-gray-500 mt-1">This is what you pay suppliers. Used for profit reports.</p>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Supplier</label>
        <select name="supplier_id" class="w-full border p-2 rounded">
            <option value="">-- None --</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Description</label>
        <textarea name="description" class="w-full border p-2 rounded"></textarea>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Product</button>
    <a href="{{ route('products.index') }}" class="ml-2 text-gray-600">Cancel</a>
</form>
@endsection