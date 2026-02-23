@php
    $role = auth()->user()->role ?? 'cashier';
    $isAdmin = $role === 'admin';
    $isManager = in_array($role, ['admin','manager'], true);
    $isCashier = in_array($role, ['admin','manager','cashier'], true);
@endphp

<div class="h-screen bg-blue-800 text-blue-50 flex flex-col">
    <div class="px-6 py-6 border-b border-blue-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/10 flex items-center justify-center font-black">
                SW
            </div>
            <div>
                <div class="text-lg font-extrabold leading-tight">Saucy Wing</div>
                <div class="text-xs text-blue-200">Franchise-ready POS</div>
            </div>
        </div>

        @if($isManager && \Illuminate\Support\Facades\Route::has('branches.switch'))
            <form method="POST" action="{{ route('branches.switch') }}" class="mt-4">
                @csrf
                <label class="text-xs text-blue-200">Current Branch</label>
                <select name="branch_id" onchange="this.form.submit()"
                    class="mt-1 w-full text-sm rounded bg-blue-900 border border-blue-700 p-2">
                    @foreach(\App\Models\Branch::orderBy('name')->get() as $b)
                        <option value="{{ $b->id }}" @selected(session('branch_id') == $b->id)>{{ $b->name }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    <nav class="flex-1 overflow-y-auto py-4">
        <a href="{{ route('dashboard') }}"
           class="nav-item flex items-center gap-3 px-6 py-3 hover:bg-blue-700/60 {{ request()->routeIs('dashboard') ? 'bg-blue-700/60' : '' }}">
            <i class="fas fa-chart-line w-5"></i>
            <span>Dashboard</span>
        </a>

        @if($isCashier && \Illuminate\Support\Facades\Route::has('orders.pos'))
            <a href="{{ route('orders.pos') }}"
               class="nav-item flex items-center gap-3 px-6 py-3 hover:bg-blue-700/60 {{ request()->routeIs('orders.pos') ? 'bg-blue-700/60' : '' }}">
                <i class="fas fa-cash-register w-5"></i>
                <span>POS</span>
            </a>
        @endif

        @if($isCashier && \Illuminate\Support\Facades\Route::has('orders.index'))
            <a href="{{ route('orders.index') }}"
               class="nav-item flex items-center gap-3 px-6 py-3 hover:bg-blue-700/60 {{ request()->routeIs('orders.*') && !request()->routeIs('orders.pos') ? 'bg-blue-700/60' : '' }}">
                <i class="fas fa-receipt w-5"></i>
                <span>Orders</span>
            </a>
        @endif

        @if($isManager && \Illuminate\Support\Facades\Route::has('products.index'))
            <a href="{{ route('products.index') }}"
               class="nav-item flex items-center gap-3 px-6 py-3 hover:bg-blue-700/60 {{ request()->routeIs('products.*') ? 'bg-blue-700/60' : '' }}">
                <i class="fas fa-boxes w-5"></i>
                <span>Products</span>
            </a>
        @endif

        @if($isManager && \Illuminate\Support\Facades\Route::has('categories.index'))
            <a href="{{ route('categories.index') }}"
               class="nav-item flex items-center gap-3 px-6 py-3 hover:bg-blue-700/60 {{ request()->routeIs('categories.*') ? 'bg-blue-700/60' : '' }}">
                <i class="fas fa-tags w-5"></i>
                <span>Categories</span>
            </a>
        @endif

        @if($isManager && \Illuminate\Support\Facades\Route::has('tables.index'))
            <a href="{{ route('tables.index') }}"
               class="nav-item flex items-center gap-3 px-6 py-3 hover:bg-blue-700/60 {{ request()->routeIs('tables.*') ? 'bg-blue-700/60' : '' }}">
                <i class="fas fa-chair w-5"></i>
                <span>Tables</span>
            </a>
        @endif

        @if($isManager && \Illuminate\Support\Facades\Route::has('inventory.index'))
            <a href="{{ route('inventory.index') }}"
               class="nav-item flex items-center gap-3 px-6 py-3 hover:bg-blue-700/60 {{ request()->routeIs('inventory.*') ? 'bg-blue-700/60' : '' }}">
                <i class="fas fa-warehouse w-5"></i>
                <span>Inventory</span>
            </a>
        @endif

        @if($isManager && \Illuminate\Support\Facades\Route::has('suppliers.index'))
            <a href="{{ route('suppliers.index') }}"
               class="nav-item flex items-center gap-3 px-6 py-3 hover:bg-blue-700/60 {{ request()->routeIs('suppliers.*') ? 'bg-blue-700/60' : '' }}">
                <i class="fas fa-truck w-5"></i>
                <span>Suppliers</span>
            </a>
        @endif

        @if($isManager && \Illuminate\Support\Facades\Route::has('expenses.index'))
            <a href="{{ route('expenses.index') }}"
               class="nav-item flex items-center gap-3 px-6 py-3 hover:bg-blue-700/60 {{ request()->routeIs('expenses.*') ? 'bg-blue-700/60' : '' }}">
                <i class="fas fa-wallet w-5"></i>
                <span>Expenses</span>
            </a>
        @endif

        @if($isManager && \Illuminate\Support\Facades\Route::has('reports.profit'))
            <a href="{{ route('reports.profit') }}"
               class="nav-item flex items-center gap-3 px-6 py-3 hover:bg-blue-700/60 {{ request()->routeIs('reports.*') ? 'bg-blue-700/60' : '' }}">
                <i class="fas fa-chart-pie w-5"></i>
                <span>Reports</span>
            </a>
        @endif

        @if($isManager && \Illuminate\Support\Facades\Route::has('qr.index'))
            <a href="{{ route('qr.index') }}"
               class="nav-item flex items-center gap-3 px-6 py-3 hover:bg-blue-700/60 {{ request()->routeIs('qr.*') ? 'bg-blue-700/60' : '' }}">
                <i class="fas fa-qrcode w-5"></i>
                <span>QR Ordering</span>
            </a>
        @endif

        @if($isCashier && \Illuminate\Support\Facades\Route::has('attendance.index'))
            <a href="{{ route('attendance.index') }}"
               class="nav-item flex items-center gap-3 px-6 py-3 hover:bg-blue-700/60 {{ request()->routeIs('attendance.*') ? 'bg-blue-700/60' : '' }}">
                <i class="fas fa-user-check w-5"></i>
                <span>Attendance</span>
            </a>
        @endif

        @if($isManager && \Illuminate\Support\Facades\Route::has('branches.index'))
            <a href="{{ route('branches.index') }}"
               class="nav-item flex items-center gap-3 px-6 py-3 hover:bg-blue-700/60 {{ request()->routeIs('branches.*') ? 'bg-blue-700/60' : '' }}">
                <i class="fas fa-store w-5"></i>
                <span>Branches</span>
            </a>
        @endif
    </nav>

    <div class="border-t border-blue-700 p-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-white/10 border border-white/10 flex items-center justify-center">
                <i class="fas fa-user"></i>
            </div>
            <div class="min-w-0">
                <div class="text-sm font-semibold truncate">{{ auth()->user()->name }}</div>
                <div class="text-xs text-blue-200 truncate">{{ auth()->user()->email }}</div>
                <div class="text-xs text-blue-200 capitalize">{{ $role }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full bg-red-600 hover:bg-red-700 text-white rounded-lg py-2 flex items-center justify-center gap-2">
                <i class="fas fa-right-from-bracket"></i>
                Logout
            </button>
        </form>
    </div>
</div>
