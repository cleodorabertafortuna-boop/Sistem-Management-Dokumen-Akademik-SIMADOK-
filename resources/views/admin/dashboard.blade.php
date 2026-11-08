@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Administrator')
@section('user-initial', 'A')

@section('sidebar-menu')
    @php
        $adminLogin = Auth::guard('web')->user();
        $namaAdmin = $adminLogin->nama ?? $adminLogin->name ?? 'Admin';

        $menus = [
            [
                'route' => 'admin.dashboard',
                'match' => ['admin.dashboard'],
                'label' => 'Dashboard',
                'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            ],
            [
                'route' => 'admin.guru.index',
                'match' => ['admin.guru.*'],
                'label' => 'Data Guru',
                'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            ],
            [
                'route' => 'admin.siswa.index',
                'match' => ['admin.siswa.*'],
                'label' => 'Data Siswa',
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            ],
        ];
    @endphp

    <div class="mx-1 mb-4 flex items-center gap-3 rounded-xl bg-white/5 px-3 py-3 ring-1 ring-white/10">
        <span
            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 text-sm font-bold text-white"
            aria-hidden="true">{{ strtoupper(mb_substr($namaAdmin, 0, 1)) }}</span>
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-white">{{ $namaAdmin }}</p>
            <p class="truncate text-xs text-primary-200/80">Administrator</p>
        </div>
    </div>

    <nav class="space-y-1" aria-label="Menu admin">
        @foreach($menus as $m)
            @php $active = request()->routeIs(...$m['match']); @endphp
            <a href="{{ route($m['route']) }}" @if($active) aria-current="page" @endif class="relative mx-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60
                            {{ $active ? 'bg-white/15 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                @if($active)
                    <span class="absolute -left-1 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r-full bg-white"></span>
                @endif
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $m['icon'] }}" />
                </svg>
                <span class="flex-1 truncate">{{ $m['label'] }}</span>
            </a>
        @endforeach
    </nav>
@endsection

@section('content')
    @php
        // Kartu statistik
        $stats = [
            ['label' => 'Total guru', 'value' => $totalGuru, 'url' => route('admin.guru.index'), 'chip' => 'bg-emerald-50 text-emerald-700', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ['label' => 'Total siswa', 'value' => $totalSiswa, 'url' => route('admin.siswa.index'), 'chip' => 'bg-blue-50 text-blue-700', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label' => 'Total kelas', 'value' => $totalKelas, 'url' => null, 'chip' => 'bg-purple-50 text-purple-700', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
        ];

        $card = 'rounded-2xl border border-slate-200 bg-white shadow-sm';

        // Avatar inisial: warna ditentukan dari nama
        $avatar = function ($name) {
            $palette = ['bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700', 'bg-purple-100 text-purple-700', 'bg-amber-100 text-amber-800', 'bg-rose-100 text-rose-700', 'bg-sky-100 text-sky-700'];
            $color = $palette[abs(crc32((string) $name)) % count($palette)];
            return '<span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold ' . $color . '" aria-hidden="true">'
                . e(strtoupper(mb_substr((string) $name, 0, 1))) . '</span>';
        };
    @endphp

    {{-- STATISTIK --}}
    <div class="mb-4 grid grid-cols-1 gap-3 sm:mb-6 sm:grid-cols-3 sm:gap-4">
        @foreach($stats as $st)
            @php $tag = $st['url'] ? 'a' : 'div'; @endphp
            <{{ $tag }} @if($st['url']) href="{{ $st['url'] }}" @endif
                class="{{ $card }} flex items-center justify-between gap-3 px-5 py-4 {{ $st['url'] ? 'transition hover:border-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500' : '' }}">
                <div>
                    <p class="text-sm text-slate-500">{{ $st['label'] }}</p>
                    <p class="mt-1 text-3xl font-bold text-slate-900">{{ $st['value'] }}</p>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $st['chip'] }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $st['icon'] }}" />
                    </svg>
                </span>
            </{{ $tag }}>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-4 sm:gap-6 xl:grid-cols-2">

        {{-- GURU TERBARU --}}
        <section class="{{ $card }} overflow-hidden">
            <header class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-4 sm:px-6">
                <h2 class="text-base font-bold text-slate-900">Guru terbaru</h2>
                <a href="{{ route('admin.guru.index') }}"
                    class="text-sm font-semibold text-blue-700 hover:text-blue-900">Lihat semua</a>
            </header>

            <ul class="divide-y divide-slate-100">
                @forelse($gurus as $g)
                    <li class="table-row-hover flex items-center gap-3 px-4 py-3.5 sm:px-6">
                        {!! $avatar($g->nama) !!}
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $g->nama }}</p>
                            <p class="text-xs text-slate-500">NIP {{ $g->nip }}</p>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-1">
                            @if($g->kelas_dibina)
                                <span
                                    class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-200">{{ $g->kelas_dibina }}</span>
                            @endif
                            <span class="text-xs text-slate-400">{{ $g->siswas_count }} siswa</span>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-12 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada data guru</p>
                        <p class="mt-1 text-sm text-slate-400">Guru yang ditambahkan akan muncul di sini.</p>
                    </li>
                @endforelse
            </ul>
        </section>

        {{-- SISWA TERBARU --}}
        <section class="{{ $card }} overflow-hidden">
            <header class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-4 sm:px-6">
                <h2 class="text-base font-bold text-slate-900">Siswa terbaru</h2>
                <a href="{{ route('admin.siswa.index') }}"
                    class="text-sm font-semibold text-blue-700 hover:text-blue-900">Lihat semua</a>
            </header>

            <ul class="divide-y divide-slate-100">
                @forelse($siswas as $s)
                    <li class="table-row-hover flex items-center gap-3 px-4 py-3.5 sm:px-6">
                        {!! $avatar($s->nama) !!}
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $s->nama }}</p>
                            <p class="text-xs text-slate-500">NIS {{ $s->nis }}</p>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-1">
                            <span
                                class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-200">{{ $s->kelas }}</span>
                            <span class="max-w-[9rem] truncate text-xs text-slate-400">Wali: {{ $s->guru->nama ?? '-' }}</span>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-12 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada data siswa</p>
                        <p class="mt-1 text-sm text-slate-400">Siswa yang ditambahkan akan muncul di sini.</p>
                    </li>
                @endforelse
            </ul>
        </section>
    </div>
@endsection