@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-xl font-bold mb-4">Edit Promotion</h1>

    <form method="POST" action="{{ route('promotions.update', $promotion) }}" class="bg-white border rounded p-4 space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium">Name</label>
                <input name="name" value="{{ old('name', $promotion->name) }}" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="text-sm font-medium">Code</label>
                <input name="code" value="{{ old('code', $promotion->code) }}" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="text-sm font-medium">Type</label>
                <select name="type" class="w-full border rounded p-2">
                    <option value="percent" {{ old('type',$promotion->type)=== 'percent' ? 'selected' : '' }}>Percent</option>
                    <option value="fixed" {{ old('type',$promotion->type)=== 'fixed' ? 'selected' : '' }}>Fixed</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium">Value</label>
                <input name="value" type="number" step="0.01" value="{{ old('value', $promotion->value) }}" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="text-sm font-medium">Branch (optional)</label>
                <select name="branch_id" class="w-full border rounded p-2">
                    <option value="">All branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ (string)old('branch_id',$promotion->branch_id) === (string)$b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-medium">Active</label>
                <select name="is_active" class="w-full border rounded p-2">
                    <option value="1" {{ old('is_active',$promotion->is_active)?'selected':'' }}>Yes</option>
                    <option value="0" {{ !old('is_active',$promotion->is_active)?'selected':'' }}>No</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('promotions.index') }}" class="px-4 py-2 border rounded">Cancel</a>
            <button class="px-4 py-2 bg-gray-900 text-white rounded">Save</button>
        </div>
    </form>
</div>
@endsection
