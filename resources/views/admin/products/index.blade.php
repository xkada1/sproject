@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Products</h1>
    <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Product</a>
</div>

<!-- Search & Filter -->
<form method="GET" class="bg-white p-4 rounded-lg shadow-md mb-6 flex gap-4">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="border p-2 rounded flex-1">
    <select name="category_id" class="border p-2 rounded">
        <option value="">All Categories</option>
        @foreach(\App\Models\Category::all() as $category)
            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
    <a href="{{ route('products.index') }}" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Reset</a>
</form>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-200">
            <tr class="text-left">
                <th class="p-3">ID</th>
                <th class="p-3">Name</th>
                <th class="p-3">Category</th>
                <th class="p-3">Price</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3">{{ $product->id }}</td>
                <td class="p-3 font-medium">{{ $product->name }}</td>
                <td class="p-3">{{ $product->category->name ?? 'N/A' }}</td>
                <td class="p-3 font-bold">₱{{ number_format($product->price, 2) }}</td>
                <td class="p-3">
                    <a href="{{ route('products.edit', $product->id) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?');">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-6 text-center text-gray-500">No products found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection