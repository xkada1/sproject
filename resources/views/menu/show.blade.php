<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="max-w-4xl mx-auto p-4">
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500">Table</div>
                <div class="text-xl font-bold">{{ $table->name }}</div>
            </div>
            <div class="text-sm text-gray-600">Order Type: Dine-in</div>
        </div>
        <p class="text-xs text-gray-500 mt-2">This is a customer ordering page. After submitting, staff will see the order in the Orders list.</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('menu.order', $table->qr_token) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @csrf

        <div class="md:col-span-2 space-y-4">
            @foreach($categories as $category)
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="font-bold mb-2">{{ $category->name }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($category->products as $product)
                            <div class="border rounded p-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-medium truncate">{{ $product->name }}</div>
                                    <div class="text-sm text-green-700 font-bold">₱{{ number_format($product->price, 2) }}</div>
                                </div>
                                <input type="number" name="items[{{ $product->id }}]" min="0" value="0" class="w-20 border rounded p-2 text-center">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-white rounded-lg shadow p-4 h-fit">
            <h2 class="font-bold mb-2">Notes</h2>
            <textarea name="notes" rows="4" class="w-full border rounded p-2" placeholder="Optional notes...">{{ old('notes') }}</textarea>
            <button type="submit" class="w-full mt-3 bg-blue-600 text-white py-3 rounded font-bold">Submit Order</button>
            <p class="text-xs text-gray-500 mt-2">Staff will confirm and process your order.</p>
        </div>
    </form>
</div>
</body>
</html>
