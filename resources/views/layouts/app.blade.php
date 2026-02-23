<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Saucy Wing')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('head')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        * { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body class="bg-gray-50 overflow-hidden">
<div class="flex h-screen w-screen">

    {{-- Sidebar --}}
    <aside id="sidebar" class="w-72 flex-shrink-0">
        @include('components.sidebar')
    </aside>

    {{-- Main content --}}
    <div class="flex-1 min-w-0 flex flex-col">
        {{-- Top bar --}}
        <header class="bg-white border-b px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button type="button" onclick="toggleSidebar()" class="bg-gray-50 border px-3 py-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-bars text-gray-700"></i>
                </button>
                <h2 class="text-xl font-bold text-gray-800">@yield('title', 'Dashboard')</h2>
            </div>
            <div class="text-sm text-gray-500">
                {{ now()->format('l, F d, Y') }}
            </div>
        </header>

        {{-- Page content scrolls here --}}
        <main class="flex-1 min-h-0 overflow-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        // hide/show sidebar
        if (sidebar.classList.contains('hidden')) {
            sidebar.classList.remove('hidden');
        } else {
            sidebar.classList.add('hidden');
        }
    }
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@stack('scripts')
</body>
</html>