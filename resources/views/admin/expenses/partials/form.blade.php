@php $e = $expense; @endphp

<div>
    <label class="block text-sm font-medium">Branch</label>
    <select name="branch_id" class="w-full border rounded p-2">
        @foreach($branches as $branch)
            <option value="{{ $branch->id }}" @selected(old('branch_id', $e?->branch_id) == $branch->id)>
                {{ $branch->name }}
            </option>
        @endforeach
    </select>
</div>

<div>
    <label class="block text-sm font-medium">Category</label>
    <input name="category" value="{{ old('category', $e?->category) }}" class="w-full border rounded p-2" placeholder="Rent, Utilities, Supplies" required>
</div>

<div>
    <label class="block text-sm font-medium">Amount</label>
    <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount', $e?->amount) }}" class="w-full border rounded p-2" required>
</div>

<div>
    <label class="block text-sm font-medium">Spent At</label>
    <input type="datetime-local" name="spent_at" value="{{ old('spent_at', optional($e?->spent_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}" class="w-full border rounded p-2" required>
</div>

<div>
    <label class="block text-sm font-medium">Description (optional)</label>
    <textarea name="description" class="w-full border rounded p-2" rows="3">{{ old('description', $e?->description) }}</textarea>
</div>