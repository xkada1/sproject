@extends('layouts.app')

@section('title', 'Product Logs')

@section('content')
<div class="bg-white p-4 rounded shadow">
    <h1 class="text-xl font-bold mb-4">
        Inventory Logs - {{ $product->name }}
    </h1>

    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-3">Date</th>
                <th class="p-3">Action</th>
                <th class="p-3">Qty</th>
                <th class="p-3">Reference</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr class="border-b">
                    <td class="p-3">{{ $log->created_at }}</td>
                    <td class="p-3">{{ ucfirst($log->action) }}</td>
                    <td class="p-3">{{ $log->qty }}</td>
                    <td class="p-3">{{ $log->reference }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $logs->links() }}
</div>
@endsection