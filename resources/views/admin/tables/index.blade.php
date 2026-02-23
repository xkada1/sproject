@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Tables Management</h1>
    <button onclick="openModal('addTableModal')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        + Add Table
    </button>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @foreach($tables as $table)
    <div class="bg-white p-4 rounded-lg shadow-md {{ $table->status === 'occupied' ? 'border-2 border-red-500' : 'border-2 border-green-500' }}">
        <div class="flex justify-between items-center">
            <h3 class="font-bold text-lg">{{ $table->name }}</h3>
            <span class="px-2 py-1 text-sm rounded {{ $table->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ ucfirst($table->status) }}
            </span>
        </div>
        <p class="text-gray-500">Capacity: {{ $table->capacity }} persons</p>
        <div class="mt-3 flex gap-2">
            <button onclick="openEditModal({{ $table->id }}, '{{ $table->name }}', {{ $table->capacity }})" class="text-blue-500 text-sm hover:underline">
                Edit
            </button>
            <form action="{{ route('tables.destroy', $table->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this table?');">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-500 text-sm hover:underline">Delete</button>
            </form>
        </div>
    </div>
    @endforeach
</div>

<!-- Add Table Modal -->
<div id="addTableModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-white p-6 rounded-lg w-96">
        <h2 class="text-xl font-bold mb-4">Add New Table</h2>
        <form action="{{ route('tables.store') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Table Name (e.g., Table 1)" class="w-full border p-2 rounded mb-3" required>
            <input type="number" name="capacity" placeholder="Capacity" class="w-full border p-2 rounded mb-3" required>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
                <button type="button" onclick="closeModal('addTableModal')" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Table Modal -->
<div id="editTableModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-white p-6 rounded-lg w-96">
        <h2 class="text-xl font-bold mb-4">Edit Table</h2>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <input type="text" name="name" id="editName" placeholder="Table Name" class="w-full border p-2 rounded mb-3" required>
            <input type="number" name="capacity" id="editCapacity" placeholder="Capacity" class="w-full border p-2 rounded mb-3" required>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
                <button type="button" onclick="closeModal('editTableModal')" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
        document.getElementById(modalId).classList.add('hidden');
    }

    function openEditModal(id, name, capacity) {
        document.getElementById('editName').value = name;
        document.getElementById('editCapacity').value = capacity;
        document.getElementById('editForm').action = '/tables/' + id;
        openModal('editTableModal');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('fixed') && event.target.id !== 'editForm') {
            const modals = document.querySelectorAll('.fixed');
            modals.forEach(modal => {
                if (modal.style.display === 'flex') {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                }
            });
        }
    }
</script>
@endsection