<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .paper { width: 58mm; padding: 6mm; }
        h2,h3,p { margin: 0; padding: 0; }
        .center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 8px 0; }
        .row { display:flex; justify-content:space-between; font-size: 12px; }
        .small { font-size: 11px; }
        .bold { font-weight: 700; }
        @media print { .no-print { display:none; } }
    </style>
</head>
<body>
<div class="paper">
    <div class="center">
        <h3>🍽️ Saucy Wing</h3>
        <p class="small">Order #{{ $order->id }}</p>
        <p class="small">{{ $order->created_at->format('M d, Y h:i A') }}</p>
        <p class="small">Type: {{ ucfirst($order->order_type) }}</p>
        <p class="small">Table: {{ $order->table->name ?? '—' }}</p>
        <p class="small">Cashier: {{ $order->user->name ?? '—' }}</p>
    </div>

    <div class="line"></div>

    @foreach($order->orderItems as $item)
        <div class="row">
            <div class="small">{{ $item->quantity }}x {{ $item->product->name }}</div>
            <div class="small">₱{{ number_format($item->price * $item->quantity, 2) }}</div>
        </div>
    @endforeach

    <div class="line"></div>

    @php
        $subtotal = $order->discount > 0 ? ($order->total_amount / (1 - ($order->discount/100))) : $order->total_amount;
        $discountAmount = $subtotal - $order->total_amount;
    @endphp

    <div class="row"><span>Subtotal</span><span>₱{{ number_format($subtotal,2) }}</span></div>
    @if($order->discount > 0)
        <div class="row"><span>Discount ({{ $order->discount }}%)</span><span>-₱{{ number_format($discountAmount,2) }}</span></div>
    @endif
    <div class="row bold"><span>Total</span><span>₱{{ number_format($order->total_amount,2) }}</span></div>

    @if($order->payment_method)
        <div class="line"></div>
        <div class="row"><span>Payment</span><span>{{ strtoupper($order->payment_method) }}</span></div>
        @if($order->payment_method === 'cash')
            <div class="row"><span>Cash</span><span>₱{{ number_format($order->amount_tendered ?? 0,2) }}</span></div>
            <div class="row"><span>Change</span><span>₱{{ number_format($order->change_amount ?? 0,2) }}</span></div>
        @endif
    @endif

    @if($order->notes)
        <div class="line"></div>
        <p class="small"><b>Notes:</b> {{ $order->notes }}</p>
    @endif

    <div class="line"></div>
    <p class="center small">Thank you! Please come again.</p>
</div>

<div class="no-print center" style="margin: 12px;">
    <button onclick="window.print()">Print</button>
    <button onclick="window.close()">Close</button>
</div>

<script>
    window.onload = function(){ window.print(); };
</script>
</body>
</html>