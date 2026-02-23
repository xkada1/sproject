@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div>
        <h1 class="text-xl font-bold">Employee Attendance</h1>
        <p class="text-sm text-gray-500">Clock in/out and view attendance logs.</p>
    </div>

    <div class="flex gap-2">
        <form method="POST" action="{{ route('attendance.clockin') }}">
            @csrf
            <button class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700"
                @disabled($todayRow && $todayRow->clock_in)>
                Clock In
            </button>
        </form>

        <form method="POST" action="{{ route('attendance.clockout') }}">
            @csrf
            <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700"
                @disabled(!$todayRow || !$todayRow->clock_in || $todayRow->clock_out)>
                Clock Out
            </button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 p-3 rounded">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-lg shadow p-4">
    <div class="flex items-center justify-between mb-3">
        <div class="font-semibold">Attendance Records</div>

        @if(in_array($role, ['admin','manager'], true))
            <form method="GET" class="flex gap-2">
                <input name="q" value="{{ $q }}" placeholder="Search employee..." class="border rounded p-2 text-sm">
                <button class="bg-gray-900 text-white px-3 rounded text-sm">Search</button>
            </form>
        @endif
    </div>

    <div class="overflow-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left">
                    <th class="py-2">Date</th>
                    <th>Employee</th>
                    <th>Clock In</th>
                    <th>Clock Out</th>
                    <th>Hours</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    @php
                        $hours = null;
                        if ($row->clock_in && $row->clock_out) {
                            $hours = $row->clock_in->diffInMinutes($row->clock_out) / 60;
                        }
                    @endphp
                    <tr class="border-b">
                        <td class="py-2">{{ $row->work_date->format('Y-m-d') }}</td>
                        <td>{{ $row->user?->name ?? '-' }}</td>
                        <td>{{ $row->clock_in ? $row->clock_in->format('H:i') : '-' }}</td>
                        <td>{{ $row->clock_out ? $row->clock_out->format('H:i') : '-' }}</td>
                        <td>{{ $hours !== null ? number_format($hours, 2) : '-' }}</td>
                    </tr>
                @empty
                    <tr><td class="py-3 text-gray-500" colspan="5">No records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
</div>
@endsection
