@php $s = $supplier; @endphp

<div>
    <label class="block text-sm font-medium">Name</label>
    <input name="name" value="{{ old('name', $s?->name) }}" class="w-full border rounded p-2" required>
</div>

<div>
    <label class="block text-sm font-medium">Contact Person</label>
    <input name="contact_person" value="{{ old('contact_person', $s?->contact_person) }}" class="w-full border rounded p-2">
</div>

<div class="grid grid-cols-2 gap-3">
    <div>
        <label class="block text-sm font-medium">Phone</label>
        <input name="phone" value="{{ old('phone', $s?->phone) }}" class="w-full border rounded p-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" value="{{ old('email', $s?->email) }}" class="w-full border rounded p-2">
    </div>
</div>

<div>
    <label class="block text-sm font-medium">Address</label>
    <textarea name="address" class="w-full border rounded p-2" rows="3">{{ old('address', $s?->address) }}</textarea>
</div>

<div class="flex items-center gap-2">
    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $s?->is_active ?? true) ? 'checked' : '' }}>
    <span class="text-sm">Active</span>
</div>