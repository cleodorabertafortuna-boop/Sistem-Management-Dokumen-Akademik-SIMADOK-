@extends('layouts.auth')

@section('title', 'Login Admin')
@section('heading', 'Login Administrator')
@section('subheading', 'Masuk dengan email dan kata sandi akun admin.')

@section('form')
    <form method="POST" action="{{ route('admin.login') }}" class="mt-6 space-y-5">
        @csrf
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
            <div class="relative">
                <span class="field-icon absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7l9 6 9-6M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                    </svg>
                </span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="Masukkan email"
                    class="h-12 w-full rounded-xl border border-slate-300 pl-11 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
            </div>
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Password</label>
            <div class="relative">
                <span class="field-icon absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="5" y="11" width="14" height="9" rx="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V8a4 4 0 118 0v3" />
                    </svg>
                </span>
                <input id="admin-password" type="password" name="password" required placeholder="Masukkan password"
                    class="h-12 w-full rounded-xl border border-slate-300 pl-11 pr-11 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                <button type="button"
                    onclick="const p=document.getElementById('admin-password'); p.type = p.type==='password' ? 'text' : 'password';"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.5 12S6 5 12 5s9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z" />
                        <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember"
                class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            Ingat saya
        </label>

        <button type="submit"
            class="h-12 w-full rounded-xl bg-brand-600 text-sm font-semibold text-white shadow-md shadow-brand-500/20 transition hover:bg-brand-700">
            Masuk
        </button>
    </form>
@endsection