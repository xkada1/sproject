@extends('layouts.app')

@section('title', 'Edit Branch')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">
    <h1 class="text-xl font-bold mb-4">Edit Branch</h1>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('branches.update', $branch) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="text-sm font-semibold">Name</label>
            <input name="name" value="{{ old('name', $branch->name) }}" class="w-full border p-2 rounded" required>
        </div>

        <div>
            <label class="text-sm font-semibold">Code</label>
            <input name="code" value="{{ old('code', $branch->code) }}" class="w-full border p-2 rounded" required>
        </div>

        <div>
            <label class="text-sm font-semibold">Address</label>
            <input name="address" value="{{ old('address', $branch->address) }}" class="w-full border p-2 rounded">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold">Phone</label>
                <input name="phone" value="{{ old('phone', $branch->phone) }}" class="w-full border p-2 rounded">
            </div>
            <div class="flex items-center gap-2 pt-6">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
                <span class="text-sm">Active</span>
            </div>
        </div>

        <div class="flex gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            <a href="{{ route('branches.index') }}" class="px-4 py-2 rounded border">Cancel</a>
        </div>
    </form>
</div>
@endsection
