<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order->id }} - Status</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> * { font-family: Poppins, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; } </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <header class="bg-white border-b">
        <div class="max-w-3xl mx-auto px-4 py-4 flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500">Table</div>
                <div class="text-lg font-bold">{{ $table->name }}</div>
            </div>
            <a href="{{ route('menu.show', $table->qr_token) }}" class="text-sm text-gray-700 underline">Back to menu</a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-6">
        @if(session('success'))
            <div class="mb-4 p-3 rounded bg-green-50 border border-green-200 text-green-800">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm text-gray-500">Order</div>
                    <div class="text-2xl font-bold">#{{ $order->id }}</div>
                    <div class="text-sm text-gray-500 mt-1">Placed: {{ $order->created_at->format('M d, Y h:i A') }}</div>
                </div>

                @php
                    $status = strtolower($order->status);
                    $badge = match($status) {
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'processing' => 'bg-blue-100 text-blue-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800',
                    };
                @endphp
                <div class="px-3 py-1 rounded-full text-sm font-semibold {{ $badge }}">{{ ucfirst($status) }}</div>
            </div>

            <div class="mt-5 border-t pt-4">
                <div class="font-semibold mb-2">Items</div>
                <div class="space-y-2">
                    @foreach($order->orderItems as $item)
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <div class="font-medium truncate">{{ $item->product->name }}</div>
                                <div class="text-xs text-gray-500">₱{{ number_format((float)$item->price, 2) }} x {{ (int)$item->quantity }}</div>
                            </div>
                            <div class="font-semibold">₱{{ number_format(((float)$item->price) * ((int)$item->quantity), 2) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-5 border-t pt-4 flex items-center justify-between">
                <div class="text-sm text-gray-500">Total</div>
                <div class="text-xl font-bold">₱{{ number_format((float)$order->total_amount, 2) }}</div>
            </div>

            @if($order->notes)
                <div class="mt-4 p-3 bg-gray-50 rounded border text-sm">
                    <div class="font-semibold mb-1">Notes</div>
                    <div class="text-gray-700">{{ $order->notes }}</div>
                </div>
            @endif

            <div class="mt-5 text-xs text-gray-500">
                Tip: Keep this page open to check status. You can refresh anytime.
            </div>
        </div>
    </main>
</body>
</html>
