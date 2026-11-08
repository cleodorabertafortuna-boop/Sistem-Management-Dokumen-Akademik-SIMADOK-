@extends('layouts.app')
@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')
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
            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 text-sm font-bold text-white"
                aria-hidden="true">{{ strtoupper(mb_substr($namaAdmin, 0, 1)) }}</span>
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-white">{{ $namaAdmin }}</p>
                <p class="truncate text-xs text-primary-200/80">Administrator</p>
            </div>
        </div>

        <nav class="space-y-1" aria-label="Menu admin">
            @foreach($menus as $m)
                @php $active = request()->routeIs(...$m['match']); @endphp
                <a href="{{ route($m['route']) }}" @if($active) aria-current="page" @endif
                    class="relative mx-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60
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
        $difilter = request('q') || request('kelas');

        // Kolom tabel di layar lebar: siswa | NIS | kelas | wali | aksi
        $cols = 'lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,0.8fr)_minmax(0,1.4fr)_auto]';

        // Gaya berulang
        $btn = 'inline-flex items-center justify-center gap-1.5 rounded-xl font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2';
        $btnPrimary = $btn . ' bg-blue-700 text-white shadow-sm hover:bg-blue-800 focus-visible:ring-blue-500';
        $btnOutline = $btn . ' border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus-visible:ring-slate-400';
        $btnOutlinePrimary = $btn . ' border border-blue-200 bg-white text-blue-700 hover:bg-blue-50 focus-visible:ring-blue-500';
        $btnOutlineDanger = $btn . ' border border-red-200 bg-white text-red-600 hover:bg-red-50 focus-visible:ring-red-500';
        $inp = 'w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20';

        // Avatar inisial: warna ditentukan dari nama
        $avatar = function ($name) {
            $palette = ['bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700', 'bg-purple-100 text-purple-700', 'bg-amber-100 text-amber-800', 'bg-rose-100 text-rose-700', 'bg-sky-100 text-sky-700'];
            $color = $palette[abs(crc32((string) $name)) % count($palette)];
            return '<span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold ' . $color . '" aria-hidden="true">'
                . e(strtoupper(mb_substr((string) $name, 0, 1))) . '</span>';
        };
    @endphp

    {{-- Hasil import --}}
    @include('admin.partials.import-result')

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Judul + tombol utama --}}
        <header class="flex flex-col gap-3 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="min-w-0">
                <h2 class="text-base font-bold text-slate-900">Data siswa</h2>
                <p class="mt-0.5 text-sm text-slate-500">Kelola data siswa, tambah satu per satu atau impor dari Excel.</p>
            </div>
            <div class="grid grid-cols-2 gap-2 sm:flex sm:shrink-0">
                <button type="button" onclick="document.getElementById('importModal').showModal()"
                    class="{{ $btnOutlinePrimary }} px-4 py-2.5 text-sm">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Excel
                </button>
                <a href="{{ route('admin.siswa.create') }}" class="{{ $btnPrimary }} px-4 py-2.5 text-sm">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Siswa
                </a>
            </div>
        </header>

        {{-- Cari & filter kelas --}}
        <form method="GET" class="border-b border-slate-100 bg-slate-50/60 px-4 py-4 sm:px-6">
            <div class="flex flex-col gap-2 sm:flex-row">
                <div class="relative w-full sm:max-w-sm sm:flex-1">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau NIS..."
                        aria-label="Cari siswa" class="{{ $inp }} pl-10">
                </div>

                <select name="kelas" onchange="this.form.submit()" aria-label="Filter kelas" class="{{ $inp }} sm:w-auto">
                    <option value="">Semua Kelas</option>
                    @foreach(\App\Support\Kelas::grouped() as $angkatan => $daftarKelas)
                        <optgroup label="Kelas {{ $angkatan }}">
                            @foreach($daftarKelas as $kelasValue)
                                <option value="{{ $kelasValue }}" @selected(request('kelas') == $kelasValue)>{{ $kelasValue }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button type="submit" class="{{ $btnPrimary }} flex-1 px-4 py-2.5 text-sm sm:flex-none">Cari</button>
                    @if($difilter)
                        <a href="{{ route('admin.siswa.index') }}" class="{{ $btnOutline }} flex-1 px-4 py-2.5 text-sm sm:flex-none">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Kartu di HP/tablet, tabel di layar lebar --}}
        <div role="table" aria-label="Data siswa">
            <div role="row"
                class="hidden gap-4 border-b border-slate-100 bg-slate-50 px-6 py-3 text-sm font-semibold text-slate-500 lg:grid {{ $cols }}">
                <div role="columnheader">Siswa</div>
                <div role="columnheader">NIS</div>
                <div role="columnheader">Kelas</div>
                <div role="columnheader">Wali kelas</div>
                <div role="columnheader" class="text-right">Aksi</div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($siswas as $s)
                    <div role="row" class="table-row-hover grid gap-3 px-4 py-4 sm:px-6 lg:items-center lg:gap-4 {{ $cols }}">

                        <div role="cell" class="flex min-w-0 items-center gap-3">
                            {!! $avatar($s->nama) !!}
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $s->nama }}</p>
                                <p class="text-xs text-slate-500 lg:hidden">NIS {{ $s->nis }}</p>
                            </div>
                        </div>

                        <div role="cell" class="hidden text-sm text-slate-500 lg:block">{{ $s->nis }}</div>

                        {{-- Kelas + wali: satu baris di HP, dua kolom di layar lebar --}}
                        <div class="flex items-center justify-between gap-3 lg:contents">
                            <div role="cell">
                                <span class="inline-flex items-center whitespace-nowrap rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-200">{{ $s->kelas }}</span>
                            </div>
                            <div role="cell" class="min-w-0 truncate text-sm text-slate-500">
                                <span class="text-xs text-slate-400 lg:hidden">Wali: </span>{{ $s->guru->nama ?? '-' }}
                            </div>
                        </div>

                        {{-- Aksi --}}
                        <div role="cell" class="grid grid-cols-3 gap-2 lg:flex lg:justify-end">
                            <a href="{{ route('admin.siswa.show', $s) }}" class="{{ $btnOutline }} px-3 py-2 text-xs">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Lihat
                            </a>
                            <a href="{{ route('admin.siswa.edit', $s) }}" class="{{ $btnOutlinePrimary }} px-3 py-2 text-xs">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.siswa.destroy', $s) }}"
                                onsubmit="return confirm('Hapus data siswa ini?')" class="contents">
                                @csrf @method('DELETE')
                                <button type="submit" class="{{ $btnOutlineDanger }} px-3 py-2 text-xs">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center px-6 py-14 text-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        @if($difilter)
                            <p class="mt-4 text-sm font-semibold text-slate-700">Tidak ada siswa yang cocok</p>
                            <p class="mt-1 max-w-xs text-sm text-slate-400">Coba ubah kata kunci atau pilih kelas lain.</p>
                            <a href="{{ route('admin.siswa.index') }}" class="{{ $btnOutline }} mt-4 px-3 py-2 text-xs">Hapus filter</a>
                        @else
                            <p class="mt-4 text-sm font-semibold text-slate-700">Belum ada data siswa</p>
                            <p class="mt-1 max-w-xs text-sm text-slate-400">Tambahkan siswa satu per satu atau impor dari file Excel.</p>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        @if($siswas->total() > 0)
            <div class="flex flex-col gap-2 border-t border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="text-sm text-slate-500">Menampilkan {{ $siswas->firstItem() }}&ndash;{{ $siswas->lastItem() }} dari {{ $siswas->total() }} data</p>
                @if($siswas->hasPages())
                    <div>{{ $siswas->withQueryString()->links() }}</div>
                @endif
            </div>
        @endif
    </section>

    {{-- Modal import --}}
    @include('admin.partials.import-modal', [
        'modalId' => 'importModal',
        'title' => 'Import Data Siswa',
        'hint' => 'Kolom: nama, nis, kelas, password (opsional, kosong = NIS)',
        'action' => route('admin.siswa.import'),
        'template' => route('admin.siswa.template'),
    ])
@endsection