@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex items-start justify-between gap-4 mb-4">
        <div>
            <h1 class="text-xl font-bold">Audit Trail</h1>
            <p class="text-sm text-gray-500">Important actions across branches.</p>
        </div>
        <form method="GET" class="flex gap-2">
            <input name="q" value="{{ $q }}" placeholder="Search action, entity, user..." class="border rounded p-2 w-72">
            <button class="bg-gray-900 text-white px-4 rounded">Search</button>
        </form>
    </div>

    <div class="bg-white rounded shadow overflow-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-50 text-left">
                    <th class="py-2 px-3">Time</th>
                    <th class="py-2 px-3">User</th>
                    <th class="py-2 px-3">Action</th>
                    <th class="py-2 px-3">Entity</th>
                    <th class="py-2 px-3">Meta</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="border-b">
                        <td class="py-2 px-3 whitespace-nowrap">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="py-2 px-3">{{ $log->user?->name ?? '—' }}</td>
                        <td class="py-2 px-3 font-mono">{{ $log->action }}</td>
                        <td class="py-2 px-3">
                            <div class="text-xs text-gray-600">{{ class_basename($log->entity_type ?? '') }}</div>
                            <div>#{{ $log->entity_id }}</div>
                        </td>
                        <td class="py-2 px-3">
                            <pre class="text-xs whitespace-pre-wrap">{{ json_encode($log->meta ?? [], JSON_PRETTY_PRINT) }}</pre>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-gray-500">No logs</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>
@endsection
