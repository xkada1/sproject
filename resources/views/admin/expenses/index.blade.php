@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between gap-4 mb-4">
        <div>
            <h1 class="text-xl font-bold">Expenses</h1>
            <p class="text-sm text-gray-500">Track expenses per branch.</p>
        </div>
        <a href="{{ route('expenses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Add Expense</a>
    </div>

    <form class="flex flex-wrap gap-2 mb-4">
        <input name="q" value="{{ $q }}" placeholder="Search category/description" class="border rounded p-2">
        <select name="branch_id" class="border rounded p-2">
            <option value="">All branches</option>
            @foreach($branches as $b)
                <option value="{{ $b->id }}" @selected((string)$branchId === (string)$b->id)>{{ $b->name }}</option>
            @endforeach
        </select>
        <button class="bg-gray-800 text-white px-4 py-2 rounded">Filter</button>
        <a href="{{ route('expenses.index') }}" class="px-4 py-2 rounded border">Reset</a>
    </form>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="overflow-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Date</th>
                    <th class="text-left">Branch</th>
                    <th class="text-left">Category</th>
                    <th class="text-left">Description</th>
                    <th class="text-right">Amount</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $e)
                    <tr class="border-b">
                        <td class="py-2">{{ optional($e->spent_at)->format('M d, Y') }}</td>
                        <td>{{ $e->branch?->name ?? '—' }}</td>
                        <td>{{ $e->category }}</td>
                        <td class="max-w-[420px] truncate">{{ $e->description }}</td>
                        <td class="text-right">₱{{ number_format($e->amount, 2) }}</td>
                        <td class="text-right">
                            <a class="text-blue-600" href="{{ route('expenses.edit', $e) }}">Edit</a>
                            <form class="inline" method="POST" action="{{ route('expenses.destroy', $e) }}" onsubmit="return confirm('Delete this expense?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-6 text-center text-gray-500">No expenses found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $expenses->links() }}</div>
</div>
@endsection
