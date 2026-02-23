@extends('layouts.app')

@section('title', 'QR Ordering')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between gap-4 mb-4">
        <div>
            <h1 class="text-xl font-bold">QR Ordering</h1>
            <p class="text-sm text-gray-500">Show and print QR codes for tables.</p>
        </div>
        <form method="GET" class="flex gap-2">
            <input name="q" value="{{ $q }}" placeholder="Search table..." class="border rounded p-2">
            <button class="bg-gray-900 text-white px-4 rounded">Search</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($tables as $table)
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-bold">Table: {{ $table->name }}</div>
                        <div class="text-sm text-gray-500">Capacity: {{ $table->capacity }}</div>
                    </div>
                    <form method="POST" action="{{ route('qr.refresh', $table) }}">
                        @csrf
                        <button class="text-sm bg-gray-100 px-3 py-1 rounded hover:bg-gray-200" type="submit">Refresh</button>
                    </form>
                </div>

                <div class="mt-3 flex items-center gap-4">
                    <div class="w-32 h-32 border rounded flex items-center justify-center" id="qr-{{ $table->id }}"></div>
                    <div class="text-sm">
                        <div class="font-medium">Menu URL</div>
                        <div class="text-xs break-all text-gray-600">{{ $table->menu_url }}</div>
                        <a class="inline-block mt-2 text-blue-600 underline" href="{{ $table->menu_url }}" target="_blank">Open Menu</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $tables->links() }}</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        @foreach($tables as $table)
            new QRCode(document.getElementById('qr-{{ $table->id }}'), {
                text: @json($table->menu_url),
                width: 120,
                height: 120,
            });
        @endforeach
    });
</script>
@endsection
