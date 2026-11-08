@extends('layouts.auth')

@section('title', 'Login Guru')
@section('heading', 'Login Guru')
@section('subheading', 'Masuk dengan nama lengkap dan NIP.')

@section('form')
    <form method="POST" action="{{ route('guru.login') }}" class="mt-6 space-y-5">
        @csrf
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama Lengkap</label>
            <div class="relative">
                <span class="field-icon absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <circle cx="12" cy="8" r="3.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 20c1.2-3.5 4-5.5 7-5.5s5.8 2 7 5.5" />
                    </svg>
                </span>
                <input type="text" name="nama" value="{{ old('nama') }}" required autofocus
                    placeholder="Masukkan nama lengkap"
                    class="h-12 w-full rounded-xl border border-slate-300 pl-11 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
            </div>
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">NIP</label>
            <div class="relative">
                <span class="field-icon absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="3.5" y="5" width="17" height="14" rx="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <circle cx="9" cy="10.5" r="1.75" stroke-linecap="round" stroke-linejoin="round" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.3 15.5c.6-1.6 1.9-2.5 2.7-2.5s2.1.9 2.7 2.5M14.5 9.5h3M14.5 12.5h3" />
                    </svg>
                </span>
                <input type="text" name="nip" required placeholder="Masukkan NIP"
                    class="h-12 w-full rounded-xl border border-slate-300 pl-11 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
            </div>
        </div>

        <button type="submit"
            class="h-12 w-full rounded-xl bg-brand-600 text-sm font-semibold text-white shadow-md shadow-brand-500/20 transition hover:bg-brand-700">
            Masuk sebagai Guru
        </button>
    </form>
@endsection