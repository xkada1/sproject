@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-xl font-bold mb-4">Create Promotion</h1>

    <form method="POST" action="{{ route('promotions.store') }}" class="bg-white border rounded p-4 space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold">Name</label>
                <input name="name" value="{{ old('name') }}" class="w-full border rounded p-2">
                @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm font-semibold">Code</label>
                <input name="code" value="{{ old('code') }}" class="w-full border rounded p-2" placeholder="PROMO10">
                @error('code') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm font-semibold">Type</label>
                <select name="type" class="w-full border rounded p-2">
                    <option value="percent" {{ old('type')==='percent'?'selected':'' }}>Percent</option>
                    <option value="fixed" {{ old('type')==='fixed'?'selected':'' }}>Fixed Amount</option>
                </select>
                @error('type') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm font-semibold">Value</label>
                <input name="value" value="{{ old('value') }}" class="w-full border rounded p-2" placeholder="10">
                @error('value') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm font-semibold">Branch (optional)</label>
                <select name="branch_id" class="w-full border rounded p-2">
                    <option value="">All branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ (string)old('branch_id')===(string)$b->id?'selected':'' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2 mt-6">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <span class="text-sm">Active</span>
            </div>
            <div>
                <label class="text-sm font-semibold">Start</label>
                <input type="datetime-local" name="start_at" value="{{ old('start_at') }}" class="w-full border rounded p-2">
            </div>
            <div>
                <label class="text-sm font-semibold">End</label>
                <input type="datetime-local" name="end_at" value="{{ old('end_at') }}" class="w-full border rounded p-2">
            </div>
        </div>

        <div class="flex gap-2">
            <button class="bg-gray-900 text-white px-4 py-2 rounded">Save</button>
            <a class="px-4 py-2 rounded border" href="{{ route('promotions.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
