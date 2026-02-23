@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-2">Branch Setup</h1>
        <p class="text-sm text-gray-500 mb-6">Create your first branch to start using Saucy Wing.</p>

        <form method="POST" action="{{ route('setup.branch.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Branch Name</label>
                <input name="name" value="{{ old('name') }}" class="w-full border rounded p-2" placeholder="Main Branch">
                @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Address (optional)</label>
                <input name="address" value="{{ old('address') }}" class="w-full border rounded p-2" placeholder="Street / City">
                @error('address') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Phone (optional)</label>
                <input name="phone" value="{{ old('phone') }}" class="w-full border rounded p-2" placeholder="+63 ...">
                @error('phone') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Create Branch
            </button>
        </form>
    </div>
</div>
@endsection
