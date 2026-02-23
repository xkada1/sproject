@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/10 border border-white/10 shadow">
                <span class="text-white text-2xl font-black">SW</span>
            </div>
            <h1 class="mt-3 text-3xl font-extrabold text-white tracking-tight">Saucy Wing</h1>
            <p class="text-sm text-slate-300 mt-1">Sign in to manage orders, inventory, and branches.</p>
        </div>

        <div class="bg-white/5 backdrop-blur rounded-2xl border border-white/10 shadow-xl p-6">
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-500/30 bg-red-500/10 p-3 text-red-200 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-200 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full rounded-lg bg-slate-900/60 border border-white/10 text-white p-3 outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="you@example.com" required autofocus>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-200 mb-1">Password</label>
                    <input type="password" name="password"
                        class="w-full rounded-lg bg-slate-900/60 border border-white/10 text-white p-3 outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="••••••••" required>
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" name="remember" class="rounded border-white/20 bg-slate-900/60">
                        Remember me
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-300 hover:text-blue-200">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <button class="w-full rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3">
                    Log In
                </button>
            </form>

            @if (Route::has('register'))
                <p class="text-center text-sm text-slate-300 mt-4">
                    Don’t have an account?
                    <a href="{{ route('register') }}" class="text-blue-300 hover:text-blue-200 font-medium">Register</a>
                </p>
            @endif
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            © {{ date('Y') }} Saucy Wing. All rights reserved.
        </p>
    </div>
</div>
@endsection
