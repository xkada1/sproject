@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Categories</h1>
    <button onclick="openModal('addCategoryModal')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        + Add Category
    </button>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @foreach($categories as $category)
    <div class="bg-white p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-bold text-lg">{{ $category->name }}</h3>
                <p class="text-gray-500">{{ $category->products->count() }} products</p>
            </div>
            <button onclick="openEditModal({{ $category->id }}, '{{ $category->name }}')" class="text-blue-600 hover:underline text-sm">
                Edit
            </button>
        </div>
        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="mt-2" onsubmit="return confirm('Delete this category and all its products?');">
            @csrf @method('DELETE')
            <button class="text-red-500 text-sm hover:underline">Delete</button>
        </form>
    </div>
    @endforeach
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-white p-6 rounded-lg w-96">
        <h2 class="text-xl font-bold mb-4">Add New Category</h2>
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Category Name" class="w-full border p-2 rounded mb-4" required>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
                <button type="button" onclick="closeModal('addCategoryModal')" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-white p-6 rounded-lg w-96">
        <h2 class="text-xl font-bold mb-4">Edit Category</h2>
        <form id="editCategoryForm" method="POST">
            @csrf @method('PUT')
            <input type="text" name="name" id="editCategoryName" class="w-full border p-2 rounded mb-4" required>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
                <button type="button" onclick="closeModal('editCategoryModal')" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
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
    function openEditModal(id, name) {
        document.getElementById('editCategoryName').value = name;
        document.getElementById('editCategoryForm').action = '/categories/' + id;
        openModal('editCategoryModal');
    }
</script>
@endsection