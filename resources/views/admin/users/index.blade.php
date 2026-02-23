@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between gap-4 mb-4">
        <div>
            <h1 class="text-xl font-bold">Users</h1>
            <p class="text-sm text-gray-500">Set role and default branch for each user.</p>
        </div>
        <form method="GET" class="flex gap-2">
            <input name="q" value="{{ $q }}" placeholder="Search name/email..." class="border rounded p-2">
            <button class="bg-gray-900 text-white px-4 rounded">Search</button>
        </form>
    </div>

    <div class="overflow-auto border rounded-lg bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-50 text-left">
                    <th class="p-3">Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Role</th>
                    <th class="p-3">Branch</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="border-b">
                        <td class="p-3">{{ $user->name }}</td>
                        <td class="p-3">{{ $user->email }}</td>
                        <td class="p-3">
                            <form method="POST" action="{{ route('users.update', $user) }}" class="flex gap-2 items-center">
                                @csrf
                                @method('PATCH')
                                <select name="role" class="border rounded p-2">
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                        </td>
                        <td class="p-3">
                                <select name="branch_id" class="border rounded p-2">
                                    <option value="">(None)</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" @selected((int)$user->branch_id === (int)$branch->id)>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                        </td>
                        <td class="p-3">
                                <button class="bg-blue-600 text-white px-3 py-2 rounded">Save</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection
