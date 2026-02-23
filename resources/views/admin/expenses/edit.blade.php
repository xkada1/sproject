@extends('layouts.app')

@section('title', 'Edit Expense')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">
    <h1 class="text-xl font-bold mb-4">Edit Expense</h1>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('expenses.update', $expense) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('admin.expenses.partials.form', ['expense' => $expense, 'branches' => $branches])
        <div class="flex gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            <a href="{{ route('expenses.index') }}" class="px-4 py-2 rounded border">Cancel</a>
        </div>
    </form>
</div>
@endsection
