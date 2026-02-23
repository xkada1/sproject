@php
    $b = $branch;
@endphp

<div>
    <label class="block text-sm font-medium">Name</label>
    <input name="name" value="{{ old('name', $b?->name) }}" class="w-full border rounded p-2" required>
</div>

<div>
    <label class="block text-sm font-medium">Code</label>
    <input name="code" value="{{ old('code', $b?->code) }}" class="w-full border rounded p-2" placeholder="e.g. MAIN" required>
</div>

<div>
    <label class="block text-sm font-medium">Address</label>
    <textarea name="address" class="w-full border rounded p-2" rows="2">{{ old('address', $b?->address) }}</textarea>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Phone</label>
        <input name="phone" value="{{ old('phone', $b?->phone) }}" class="w-full border rounded p-2">
    </div>
    <div class="flex items-center gap-2 mt-6">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $b?->is_active ?? true) ? 'checked' : '' }}>
        <span class="text-sm">Active</span>
    </div>
</div>
