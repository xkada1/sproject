<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu - Table {{ $table->name }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> * { font-family: Poppins, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; } </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <header class="sticky top-0 z-40 bg-white border-b">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500">Table</div>
                <div class="text-lg font-bold">{{ $table->name }}</div>
            </div>
            <button type="button" onclick="toggleCart(true)" class="relative bg-gray-900 text-white px-4 py-2 rounded-lg">
                Cart
                <span id="cartBadge" class="hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">0</span>
            </button>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-4">
        @if(session('success'))
            <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-800">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700">
                <div class="font-bold mb-1">Please fix:</div>
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl border p-4 mb-4">
            <div class="flex gap-2">
                <input id="q" type="text" placeholder="Search..." class="w-full border rounded-lg px-3 py-2" oninput="filterProducts()">
                <button type="button" class="border rounded-lg px-4" onclick="document.getElementById('q').value=''; filterProducts();">Clear</button>
            </div>
            <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                <button type="button" class="catBtn px-4 py-2 rounded-full bg-gray-900 text-white whitespace-nowrap" data-cat="all" onclick="setCategory('all', this)">All</button>
                @foreach($categories as $cat)
                    <button type="button" class="catBtn px-4 py-2 rounded-full bg-gray-100 text-gray-800 whitespace-nowrap" data-cat="{{ $cat->id }}" onclick="setCategory('{{ $cat->id }}', this)">{{ $cat->name }}</button>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3" id="grid">
            @foreach($categories as $cat)
                @foreach($cat->products as $p)
                    <button type="button"
                        class="productCard bg-white border rounded-xl p-3 text-left hover:shadow"
                        data-name="{{ strtolower($p->name) }}"
                        data-cat="{{ $cat->id }}"
                        onclick='addToCart({{ $p->id }}, @json($p->name), {{ (float)$p->price }})'>
                        <div class="w-full h-24 bg-gray-100 rounded-lg mb-2 flex items-center justify-center text-gray-400 overflow-hidden">
                            @if($p->image)
                                <img src="{{ asset('images/' . $p->image) }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                            @else
                                📷 No Image
                            @endif
                        </div>
                        <div class="font-semibold text-sm truncate">{{ $p->name }}</div>
                        <div class="text-green-700 font-bold">₱{{ number_format($p->price, 2) }}</div>
                        <div class="text-xs text-gray-500 mt-1">Tap to add</div>
                    </button>
                @endforeach
            @endforeach
        </div>
    </main>

    <!-- Cart Drawer -->
    <div id="cartOverlay" class="fixed inset-0 bg-black/30 hidden" onclick="toggleCart(false)"></div>
    <aside id="cartDrawer" class="fixed right-0 top-0 h-full w-full sm:w-[420px] bg-white shadow-2xl translate-x-full transition-transform z-50 flex flex-col">
        <div class="p-4 border-b flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500">Your Order</div>
                <div class="text-lg font-bold">Table {{ $table->name }}</div>
            </div>
            <button type="button" class="text-gray-600" onclick="toggleCart(false)">✕</button>
        </div>

        <div class="flex-1 overflow-auto p-4" id="cartItems">
            <div class="text-gray-500 text-center" id="emptyCart">No items yet</div>
        </div>

        <form method="POST" action="{{ route('menu.place', $table->qr_token) }}" class="p-4 border-t space-y-3" onsubmit="return beforeSubmit()">
            @csrf

            <div>
                <label class="text-sm font-semibold">Notes (optional)</label>
                <textarea name="notes" class="w-full border rounded-lg p-2 mt-1" rows="2" placeholder="e.g. No onions, extra sauce"></textarea>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold">Discount</span>
                <input name="discount" id="discount" type="number" min="0" max="100" value="0" class="border rounded-lg px-3 py-2 w-20 text-center" oninput="renderTotals()">
                <span class="text-sm text-gray-600">%</span>
            </div>

            <div class="rounded-xl bg-gray-50 border p-3 text-sm space-y-1">
                <div class="flex justify-between"><span>Subtotal</span><span id="subtotal">₱0.00</span></div>
                <div class="flex justify-between text-red-600"><span>Discount</span><span id="discountAmt">-₱0.00</span></div>
                <div class="flex justify-between text-lg font-bold"><span>Total</span><span id="total">₱0.00</span></div>
            </div>

            <div class="text-xs text-gray-500">
                Payment is handled at the counter / cashier.
            </div>

            <div id="hiddenFields"></div>

            <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-lg font-bold disabled:opacity-50" id="placeBtn" disabled>
                Place Order
            </button>
        </form>
    </aside>

<script>
    let currentCat = 'all';
    const cart = [];

    function money(n){ return '₱' + (Number(n)||0).toFixed(2); }

    function toggleCart(show){
        const drawer = document.getElementById('cartDrawer');
        const overlay = document.getElementById('cartOverlay');
        if (show) {
            overlay.classList.remove('hidden');
            drawer.classList.remove('translate-x-full');
            renderCart();
        } else {
            overlay.classList.add('hidden');
            drawer.classList.add('translate-x-full');
        }
    }

    function setCategory(cat, btn){
        currentCat = cat;
        document.querySelectorAll('.catBtn').forEach(b=>{
            b.classList.remove('bg-gray-900','text-white');
            b.classList.add('bg-gray-100','text-gray-800');
        });
        btn.classList.add('bg-gray-900','text-white');
        btn.classList.remove('bg-gray-100','text-gray-800');
        filterProducts();
    }

    function filterProducts(){
        const q = (document.getElementById('q').value||'').toLowerCase();
        document.querySelectorAll('.productCard').forEach(card=>{
            const name = (card.dataset.name||'');
            const cat = (card.dataset.cat||'');
            const okCat = (currentCat==='all') || (cat===String(currentCat));
            const okQ = name.includes(q);
            card.style.display = (okCat && okQ) ? 'block' : 'none';
        });
    }

    function addToCart(id, name, price){
        id = Number(id); price = Number(price);
        const existing = cart.find(i=>i.id===id);
        if (existing) existing.qty += 1;
        else cart.push({id, name, price, qty: 1});
        renderCart();
        toggleCart(true);
    }

    function updateQty(idx, delta){
        if (!cart[idx]) return;
        cart[idx].qty += delta;
        if (cart[idx].qty <= 0) cart.splice(idx,1);
        renderCart();
    }

    function renderCart(){
        const container = document.getElementById('cartItems');
        const empty = document.getElementById('emptyCart');
        const badge = document.getElementById('cartBadge');
        const placeBtn = document.getElementById('placeBtn');

        if (cart.length === 0) {
            empty.classList.remove('hidden');
            container.innerHTML = '';
            container.appendChild(empty);
            badge.classList.add('hidden');
            placeBtn.disabled = true;
            renderTotals();
            return;
        }

        empty.classList.add('hidden');
        let html = '';
        cart.forEach((item, idx)=>{
            html += `
                <div class="flex items-center justify-between border-b py-3">
                    <div class="min-w-0">
                        <div class="font-semibold truncate">${escapeHtml(item.name)}</div>
                        <div class="text-sm text-gray-500">${money(item.price)} x ${item.qty}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="w-9 h-9 rounded-lg bg-gray-100" onclick="updateQty(${idx},-1)">-</button>
                        <div class="w-6 text-center font-bold">${item.qty}</div>
                        <button type="button" class="w-9 h-9 rounded-lg bg-gray-100" onclick="updateQty(${idx},1)">+</button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
        const count = cart.reduce((s,i)=>s+i.qty,0);
        badge.textContent = String(count);
        badge.classList.remove('hidden');
        placeBtn.disabled = false;
        renderTotals();
    }

    function renderTotals(){
        const subtotal = cart.reduce((s,i)=>s+(i.price*i.qty),0);
        const discountPercent = Math.min(100, Math.max(0, Number(document.getElementById('discount').value)||0));
        document.getElementById('discount').value = discountPercent;
        const discountAmt = subtotal * (discountPercent/100);
        const total = Math.max(0, subtotal - discountAmt);

        document.getElementById('subtotal').textContent = money(subtotal);
        document.getElementById('discountAmt').textContent = '-'+money(discountAmt);
        document.getElementById('total').textContent = money(total);
    }

    function beforeSubmit(){
        if (cart.length === 0) return false;
        const hidden = document.getElementById('hiddenFields');
        hidden.innerHTML = '';
        cart.forEach((item, idx)=>{
            hidden.insertAdjacentHTML('beforeend', `<input type="hidden" name="items[${idx}][id]" value="${item.id}">`);
            hidden.insertAdjacentHTML('beforeend', `<input type="hidden" name="items[${idx}][qty]" value="${item.qty}">`);
        });
        return true;
    }

    function escapeHtml(str){
        return String(str)
            .replaceAll('&','&amp;')
            .replaceAll('<','&lt;')
            .replaceAll('>','&gt;')
            .replaceAll('"','&quot;')
            .replaceAll("'",'&#039;');
    }

    // Initial
    filterProducts();
</script>

</body>
</html>
