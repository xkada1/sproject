@extends('layouts.app')

@section('title', 'Profit Report')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between flex-wrap gap-4 mb-4">
        <div>
            <h1 class="text-xl font-bold">Profit Report</h1>
            <p class="text-sm text-gray-500">Completed orders only</p>
        </div>

        <form class="flex items-end gap-2" method="GET" action="{{ route('reports.profit') }}">
            <div>
                <label class="block text-xs text-gray-500">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="border rounded p-2">
            </div>
            <div>
                <label class="block text-xs text-gray-500">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="border rounded p-2">
            </div>
            <div>
                <label class="block text-xs text-gray-500">Branch</label>
                <select name="branch_id" class="border rounded p-2">
                    <option value="">All</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" @selected(request('branch_id') == $b->id)>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-gray-50 p-4 rounded border">
            <div class="text-xs text-gray-500">Revenue</div>
            <div class="text-xl font-bold">₱{{ number_format($revenue, 2) }}</div>
        </div>
        <div class="bg-gray-50 p-4 rounded border">
            <div class="text-xs text-gray-500">COGS (Cost)</div>
            <div class="text-xl font-bold">₱{{ number_format($cogs, 2) }}</div>
        </div>
        <div class="bg-gray-50 p-4 rounded border">
            <div class="text-xs text-gray-500">Gross Profit</div>
            <div class="text-xl font-bold">₱{{ number_format($grossProfit, 2) }}</div>
        </div>
        <div class="bg-gray-50 p-4 rounded border">
            <div class="text-xs text-gray-500">Expenses</div>
            <div class="text-xl font-bold">₱{{ number_format($expensesTotal, 2) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="border rounded p-4">
            <div class="font-bold mb-2">Top Products (Profit)</div>
            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b text-left"><th class="py-2">Product</th><th>Qty</th><th>Profit</th></tr></thead>
                    <tbody>
                    @forelse($topProducts as $row)
                        <tr class="border-b">
                            <td class="py-2">{{ $row->name }}</td>
                            <td>{{ (int)$row->qty }}</td>
                            <td>₱{{ number_format($row->profit, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-3 text-gray-500" colspan="3">No data</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="border rounded p-4">
            <div class="font-bold mb-2">Daily Summary</div>
            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b text-left"><th class="py-2">Date</th><th>Orders</th><th>Revenue</th><th>Gross Profit</th></tr></thead>
                    <tbody>
                    @forelse($daily as $d)
                        <tr class="border-b">
                            <td class="py-2">{{ $d->day }}</td>
                            <td>{{ (int)$d->orders }}</td>
                            <td>₱{{ number_format($d->revenue, 2) }}</td>
                            <td>₱{{ number_format($d->gross_profit, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-3 text-gray-500" colspan="4">No data</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
