@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex items-start justify-between gap-4 mb-4">
        <div>
            <h1 class="text-xl font-bold">Promotions</h1>
            <p class="text-sm text-gray-500">Create promo codes (percent or fixed). Optionally target a branch.</p>
        </div>
        <a href="{{ route('promotions.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded">New Promotion</a>
    </div>

    <form method="GET" class="flex gap-2 mb-4">
        <input name="q" value="{{ $q }}" placeholder="Search name/code..." class="border rounded p-2 w-full">
        <button class="bg-gray-900 text-white px-4 rounded">Search</button>
    </form>

    <div class="bg-white rounded shadow overflow-auto">
        <table class="w-full text-sm">
            <thead class="border-b bg-gray-50">
                <tr>
                    <th class="text-left p-3">Name</th>
                    <th class="text-left p-3">Code</th>
                    <th class="text-left p-3">Type</th>
                    <th class="text-left p-3">Value</th>
                    <th class="text-left p-3">Branch</th>
                    <th class="text-left p-3">Active</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($promotions as $p)
                <tr class="border-b">
                    <td class="p-3 font-medium">{{ $p->name }}</td>
                    <td class="p-3"><span class="px-2 py-1 rounded bg-gray-100">{{ $p->code }}</span></td>
                    <td class="p-3">{{ strtoupper($p->type) }}</td>
                    <td class="p-3">{{ $p->type === 'percent' ? $p->value.'%' : '₱'.number_format($p->value,2) }}</td>
                    <td class="p-3">{{ $p->branch?->name ?? 'All' }}</td>
                    <td class="p-3">{!! $p->is_active ? '<span class="text-green-700">Yes</span>' : '<span class="text-gray-500">No</span>' !!}</td>
                    <td class="p-3 text-right">
                        <a class="text-blue-700" href="{{ route('promotions.edit', $p) }}">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td class="p-4 text-gray-500" colspan="7">No promotions</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $promotions->links() }}</div>
</div>
@endsection
