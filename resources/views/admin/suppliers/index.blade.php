@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between gap-4 mb-4">
        <div>
            <h1 class="text-xl font-bold">Suppliers</h1>
            <p class="text-sm text-gray-500">Suppliers for purchasing/stock receipts.</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ New Supplier</a>
    </div>

    <form method="GET" class="flex gap-2 mb-4">
        <input name="q" value="{{ $q }}" placeholder="Search supplier..." class="flex-1 border rounded p-2">
        <button class="bg-gray-800 text-white px-4 rounded">Search</button>
    </form>

    <div class="overflow-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="py-2">Name</th>
                    <th>Contact</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th class="w-40"></th>
                </tr>
            </thead>
            <tbody>
            @foreach($suppliers as $s)
                <tr class="border-b">
                    <td class="py-2 font-medium">{{ $s->name }}</td>
                    <td>{{ $s->contact_person }}</td>
                    <td>{{ $s->phone }}</td>
                    <td>{{ $s->email }}</td>
                    <td>{{ $s->is_active ? 'Active' : 'Inactive' }}</td>
                    <td class="text-right">
                        <a class="text-blue-600" href="{{ route('suppliers.edit', $s) }}">Edit</a>
                        <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-600" onclick="return confirm('Delete supplier?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $suppliers->links() }}</div>
</div>
@endsection
