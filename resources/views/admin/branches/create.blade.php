@extends('layouts.app')

@section('title', 'Create Branch')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">
    <h1 class="text-xl font-bold mb-4">Create Branch</h1>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('branches.store') }}" class="space-y-4">
        @csrf
        @include('admin.branches.partials.form', ['branch' => null])
        <div class="flex gap-2">
            <a href="{{ route('branches.index') }}" class="px-4 py-2 rounded border">Cancel</a>
            <button class="px-4 py-2 rounded bg-blue-600 text-white">Save</button>
        </div>
    </form>
</div>
@endsection
