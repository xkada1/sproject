@extends('layouts.app')

@section('title', 'Create Supplier')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">
    <h1 class="text-xl font-bold mb-4">Create Supplier</h1>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-3">
        @csrf
        @include('admin.suppliers.partials.form', ['supplier' => null])
        <div class="flex gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            <a href="{{ route('suppliers.index') }}" class="px-4 py-2 rounded border">Cancel</a>
        </div>
    </form>
</div>
@endsection
