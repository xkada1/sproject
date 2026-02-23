@extends('layouts.app')

@section('title', 'Branches')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between gap-4 mb-4">
        <div>
            <h1 class="text-xl font-bold">Branches</h1>
            <p class="text-sm text-gray-500">Manage branches (franchise-ready).</p>
        </div>
        <a href="{{ route('branches.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ New Branch</a>
    </div>

    <form method="GET" class="flex gap-2 mb-4">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Search..." class="border rounded px-3 py-2 w-full">
        <button class="bg-gray-800 text-white px-4 py-2 rounded">Search</button>
    </form>

    <div class="overflow-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b text-left">
                    <th class="py-2">Name</th>
                    <th>Code</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($branches as $branch)
                    <tr class="border-b">
                        <td class="py-2 font-medium">{{ $branch->name }}</td>
                        <td>{{ $branch->code }}</td>
                        <td>
                            <span class="px-2 py-1 rounded text-xs {{ $branch->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $branch->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('branches.edit', $branch) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="inline" onsubmit="return confirm('Delete this branch?');">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline ml-3" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $branches->links() }}</div>
</div>
@endsection
