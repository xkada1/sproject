@extends('layouts.app')

@section('title', 'Inventory Logs')

@section('content')
<div class="bg-white rounded-lg shadow p-4">
    <h1 class="text-2xl font-bold mb-4">Inventory Logs</h1>

    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr class="text-left">
                <th class="p-3">Date</th>
                <th class="p-3">Product</th>
                <th class="p-3">Action</th>
                <th class="p-3">Qty</th>
                <th class="p-3">Reference</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr class="border-b">
                    <td class="p-3">{{ $log->created_at->format('M d H:i') }}</td>
                    <td class="p-3">{{ $log->product->name ?? 'Deleted Product' }}</td>
                    <td class="p-3 capitalize">{{ $log->action }}</td>
                    <td class="p-3">{{ $log->qty }}</td>
                    <td class="p-3">{{ $log->reference }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection