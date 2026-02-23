@extends('layouts.app')

@section('title', 'POS')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="grid grid-cols-12 gap-4 h-[calc(100vh-140px)]">

    {{-- LEFT: PRODUCTS --}}
    <div class="col-span-8 bg-white rounded-lg shadow p-4 overflow-auto">

        {{-- Search --}}
        <div class="mb-4">
            <input type="text" id="searchProducts" onkeyup="searchProducts()"
                   placeholder="🔍 Search products..."
                   class="w-full border p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Quick Add --}}
        <div class="mb-4">
            <div class="text-sm font-bold text-gray-500 mb-2">Quick Add:</div>
            <div class="flex gap-2 flex-wrap">
                @php $popularProducts = \App\Models\Product::take(6)->get(); @endphp
                @foreach($popularProducts as $product)
                    <button type="button"
                            onclick='addToOrder({{ $product->id }}, @json($product->name), {{ (float)$product->price }})'
                            class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm hover:bg-yellow-200 transition">
                        ⭐ {{ $product->name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Category Tabs --}}
        <div class="flex gap-2 mb-4 overflow-x-auto pb-2">
            <button type="button"
                    onclick="filterProducts('all', this)"
                    class="category-btn bg-blue-600 text-white px-4 py-2 rounded shadow whitespace-nowrap">
                All
            </button>

            @foreach($categories as $category)
                <button type="button"
                        onclick="filterProducts({{ $category->id }}, this)"
                        class="category-btn bg-white px-4 py-2 rounded shadow hover:bg-blue-100 whitespace-nowrap"
                        data-category="{{ $category->id }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        {{-- Products Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3" id="productsGrid">
            @foreach($categories as $category)
                @foreach($category->products as $product)
                    <button type="button"
                            class="product-card text-left bg-white p-3 rounded shadow hover:shadow-lg transition border"
                            data-category="{{ $category->id }}"
                            data-name="{{ strtolower($product->name) }}"
                            onclick='addToOrder({{ $product->id }}, @json($product->name), {{ (float)$product->price }})'>

                        @if($product->image)
                            <img src="{{ asset('images/' . $product->image) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-24 object-cover rounded mb-2">
                        @else
                            <div class="w-full h-24 bg-gray-100 rounded mb-2 flex items-center justify-center text-gray-400">
                                📷 No Image
                            </div>
                        @endif

                        <div class="font-bold text-sm">{{ $product->name }}</div>
                        <div class="text-green-600 font-bold">₱{{ number_format($product->price, 2) }}</div>
                    </button>
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- RIGHT: ORDER --}}
    <div class="col-span-4 bg-white rounded-lg shadow flex flex-col overflow-hidden">
        <div class="p-4 border-b">
            <h2 class="text-xl font-bold">Current Order</h2>

            {{-- Order Type --}}
            <div class="flex gap-3 mt-3 text-sm">
                <label class="flex items-center gap-2">
                    <input type="radio" name="order_type" value="dine-in" checked>
                    Dine-in
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="order_type" value="takeout">
                    Takeout
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="order_type" value="delivery">
                    Delivery
                </label>
            </div>

            {{-- Table Select --}}
            <select id="tableSelect" class="w-full border mt-3 p-2 rounded">
                <option value="">Select Table (Dine-in)</option>
                @foreach($tables as $table)
                    <option value="{{ $table->id }}">{{ $table->name }} ({{ $table->capacity }} seats)</option>
                @endforeach
            </select>
        </div>

        {{-- Order Items --}}
        <div id="orderItems" class="flex-1 overflow-auto p-4">
            <p class="text-gray-500 text-center" id="emptyMsg">No items added yet</p>
        </div>

        {{-- Bottom --}}
        <div class="p-4 border-t space-y-3">
            <textarea id="orderNotes"
                      placeholder="📝 Order notes..."
                      class="w-full border p-2 rounded text-sm"
                      rows="2"></textarea>

            <div class="flex items-center gap-2">
                <span class="text-sm font-bold">Discount:</span>
                <input type="number" id="discountInput" min="0" max="100" value="0"
                       class="border p-1 rounded w-16 text-center"
                       onchange="calculateTotal(); calculateChange();">
                <span class="text-sm">%</span>
            </div>

            {{-- Totals --}}
            <div class="text-sm space-y-1">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span id="subtotal">₱0.00</span>
                </div>
                <div class="flex justify-between text-red-500">
                    <span>Discount:</span>
                    <span id="discountAmount">-₱0.00</span>
                </div>
                <div class="flex justify-between text-xl font-bold">
                    <span>Total:</span>
                    <span id="orderTotal">₱0.00</span>
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="border rounded p-3 space-y-2">
                <div class="text-sm font-bold text-gray-700">Payment</div>

                <div class="flex gap-3 text-sm">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="pay_method" value="cash" checked onchange="syncPaymentUI(); calculateChange();">
                        Cash
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="pay_method" value="gcash" onchange="syncPaymentUI();">
                        GCash
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="pay_method" value="card" onchange="syncPaymentUI();">
                        Card
                    </label>
                </div>

                <div id="cashFields" class="space-y-2">
                    <div class="flex items-center justify-between gap-3">
                        <label class="text-sm text-gray-600">Amount Tendered</label>
                        <input type="number" id="amountTendered" min="0" step="0.01"
                               class="border p-2 rounded w-40 text-right"
                               oninput="calculateChange()"
                               placeholder="0.00">
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Change</span>
                        <span class="font-bold" id="changeDisplay">₱0.00</span>
                    </div>
                </div>
            </div>

            <button type="button"
                    onclick="submitOrder()"
                    class="w-full bg-green-600 text-white py-3 rounded font-bold hover:bg-green-700">
                🖨️ Place Order
            </button>

            <button type="button"
                    onclick="clearOrder()"
                    class="w-full bg-red-500 text-white py-2 rounded hover:bg-red-600">
                Clear Order
            </button>
        </div>
    </div>
</div>

<audio id="orderSound" preload="auto"></audio>
@endsection

@push('scripts')
<script>
(function () {
    window.orderItems = [];

    function formatMoney(n) {
        n = Number(n) || 0;
        return '₱' + n.toFixed(2);
    }

    window.addToOrder = function(id, name, price) {
        id = Number(id);
        price = Number(price);

        var existing = window.orderItems.find(function(i){ return i.id === id; });
        if (existing) existing.qty++;
        else window.orderItems.push({ id: id, name: name, price: price, qty: 1 });

        window.renderOrderItems();
    };

    window.renderOrderItems = function() {
        var container = document.getElementById('orderItems');
        if (!container) return;

        if (window.orderItems.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center" id="emptyMsg">No items added yet</p>';
            document.getElementById('orderTotal').textContent = '₱0.00';
            document.getElementById('subtotal').textContent = '₱0.00';
            document.getElementById('discountAmount').textContent = '-₱0.00';
            document.getElementById('changeDisplay').textContent = '₱0.00';
            return;
        }

        var html = '';
        window.orderItems.forEach(function(item, index) {
            html += ''
              + '<div class="flex justify-between items-center border-b py-2">'
              + '  <div class="min-w-0">'
              + '    <div class="font-bold truncate">' + window.escapeHtml(item.name) + '</div>'
              + '    <div class="text-sm text-gray-500">' + formatMoney(item.price) + ' x ' + item.qty + '</div>'
              + '  </div>'
              + '  <div class="flex items-center gap-2 flex-shrink-0">'
              + '    <button type="button" onclick="updateQty(' + index + ', -1)" class="bg-gray-200 w-7 h-7 rounded hover:bg-gray-300">-</button>'
              + '    <span class="font-bold w-5 text-center">' + item.qty + '</span>'
              + '    <button type="button" onclick="updateQty(' + index + ', 1)" class="bg-gray-200 w-7 h-7 rounded hover:bg-gray-300">+</button>'
              + '    <button type="button" onclick="removeItem(' + index + ')" class="text-red-500 ml-1 hover:text-red-700">✕</button>'
              + '  </div>'
              + '</div>';
        });

        container.innerHTML = html;
        window.calculateTotal();
        window.calculateChange();
    };

    window.calculateTotal = function() {
        var subtotal = 0;
        window.orderItems.forEach(function(i){ subtotal += i.price * i.qty; });

        var discountEl = document.getElementById('discountInput');
        var discountPercent = Math.min(100, Math.max(0, Number(discountEl.value) || 0));
        discountEl.value = discountPercent;

        var discountAmount = subtotal * (discountPercent / 100);
        var total = subtotal - discountAmount;

        document.getElementById('subtotal').textContent = formatMoney(subtotal);
        document.getElementById('discountAmount').textContent = '-' + formatMoney(discountAmount);
        document.getElementById('orderTotal').textContent = formatMoney(total);
    };

    window.updateQty = function(index, change) {
        if (!window.orderItems[index]) return;
        window.orderItems[index].qty += change;
        if (window.orderItems[index].qty <= 0) window.orderItems.splice(index, 1);
        window.renderOrderItems();
    };

    window.removeItem = function(index) {
        window.orderItems.splice(index, 1);
        window.renderOrderItems();
    };

    window.clearOrder = function() {
        if (!confirm('Clear all items from order?')) return;
        window.orderItems = [];
        document.getElementById('discountInput').value = 0;
        document.getElementById('orderNotes').value = '';
        var tender = document.getElementById('amountTendered');
        if (tender) tender.value = '';
        window.renderOrderItems();
    };

    window.filterProducts = function(categoryId, btnEl) {
        var products = document.querySelectorAll('.product-card');
        var buttons = document.querySelectorAll('.category-btn');

        buttons.forEach(function(btn){
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('bg-white');
        });

        if (btnEl) {
            btnEl.classList.add('bg-blue-600', 'text-white');
            btnEl.classList.remove('bg-white');
        }

        products.forEach(function(p){
            var ok = (categoryId === 'all') || (p.dataset.category == categoryId);
            p.style.display = ok ? 'block' : 'none';
        });
    };

    window.searchProducts = function() {
        var q = document.getElementById('searchProducts').value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(function(p){
            var name = (p.dataset.name || '').toLowerCase();
            p.style.display = name.includes(q) ? 'block' : 'none';
        });
    };

    window.escapeHtml = function(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    };

    window.syncTableSelectState = function() {
        var r = document.querySelector('input[name="order_type"]:checked');
        var selected = r ? r.value : 'dine-in';
        var tableSelect = document.getElementById('tableSelect');
        if (!tableSelect) return;

        if (selected === 'dine-in') {
            tableSelect.disabled = false;
            tableSelect.required = true;
        } else {
            tableSelect.disabled = true;
            tableSelect.required = false;
            tableSelect.value = '';
        }
    };

    // ✅ Payment UI functions
    window.syncPaymentUI = function () {
        const method = document.querySelector('input[name="pay_method"]:checked')?.value || '';
        const cashFields = document.getElementById('cashFields');
        if (!cashFields) return;

        cashFields.classList.toggle('hidden', method !== 'cash');
        if (method !== 'cash') {
            const tender = document.getElementById('amountTendered');
            if (tender) tender.value = '';
            document.getElementById('changeDisplay').textContent = '₱0.00';
        }
    };

    window.calculateChange = function () {
        const method = document.querySelector('input[name="pay_method"]:checked')?.value || '';
        if (method !== 'cash') return;

        const totalText = document.getElementById('orderTotal')?.textContent || '₱0.00';
        const total = Number(totalText.replace('₱','')) || 0;

        const tendered = Number(document.getElementById('amountTendered')?.value) || 0;
        const change = Math.max(0, tendered - total);
        document.getElementById('changeDisplay').textContent = '₱' + change.toFixed(2);
    };

    window.submitOrder = function() {
        var r = document.querySelector('input[name="order_type"]:checked');
        var orderType = r ? r.value : 'dine-in';

        var tableId = document.getElementById('tableSelect').value;
        var notes = document.getElementById('orderNotes').value;
        var discount = document.getElementById('discountInput').value;

        if (orderType === 'dine-in' && !tableId) {
            alert('Please select a table for Dine-in orders!');
            return;
        }

        if (window.orderItems.length === 0) {
            alert('Please add items to the order!');
            return;
        }

        // ✅ Payment fields
        const payMethod = document.querySelector('input[name="pay_method"]:checked')?.value || '';
        const amountTendered = document.getElementById('amountTendered')?.value || '';

        // If cash, require enough payment
        if (payMethod === 'cash') {
            const totalText = document.getElementById('orderTotal')?.textContent || '₱0.00';
            const total = Number(totalText.replace('₱','')) || 0;
            const tender = Number(amountTendered) || 0;
            if (tender < total) {
                alert('Cash received must be greater than or equal to total.');
                return;
            }
        }

        var meta = document.querySelector('meta[name="csrf-token"]');
        var csrf = meta ? meta.content : '';
        if (!csrf) {
            alert('Missing CSRF token meta tag.');
            return;
        }

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('orders.store') }}";
        form.target = '_blank';

        function addHidden(name, value) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        }

        addHidden('_token', csrf);
        addHidden('order_type', orderType);
        addHidden('table_id', orderType === 'dine-in' ? tableId : '');
        addHidden('notes', notes);
        addHidden('discount', discount);

        // ✅ send payment fields to controller
        addHidden('payment_method', payMethod);
        if (payMethod === 'cash') addHidden('amount_tendered', amountTendered);

        window.orderItems.forEach(function(item, idx){
            addHidden('items[' + idx + '][id]', item.id);
            addHidden('items[' + idx + '][qty]', item.qty);
        });

        document.body.appendChild(form);
        form.submit();
        form.remove();

        // reset UI
        window.orderItems = [];
        document.getElementById('discountInput').value = 0;
        document.getElementById('orderNotes').value = '';
        var tender = document.getElementById('amountTendered');
        if (tender) tender.value = '';
        window.renderOrderItems();
    };

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="order_type"]').forEach(function(r){
            r.addEventListener('change', window.syncTableSelectState);
        });

        window.syncTableSelectState();
        window.renderOrderItems();

        window.syncPaymentUI(); // ✅ important
        console.log('POS JS loaded OK');
    });
})();
</script>
@endpush