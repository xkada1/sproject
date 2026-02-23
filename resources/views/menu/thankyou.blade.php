<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="max-w-lg mx-auto p-6">
    <div class="bg-white shadow rounded-lg p-6 text-center">
        <h1 class="text-xl font-bold mb-2">✅ Thank you!</h1>
        <p class="text-gray-600">Your order has been sent to the kitchen.</p>
        <p class="text-gray-500 text-sm mt-2">Order #{{ $order->id }}</p>
        <a href="{{ route('menu.show', $table->qr_token) }}" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded">Back to Menu</a>
    </div>
</div>
</body>
</html>
