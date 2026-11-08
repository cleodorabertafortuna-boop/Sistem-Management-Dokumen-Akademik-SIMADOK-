@extends('layouts.app')
@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard Guru')
@section('user-initial', $guru->nama)

@section('sidebar-menu')
    @php
        $guruLogin = Auth::guard('guru')->user();

        $menus = [
            [
                'route' => 'guru.dashboard',
                'match' => ['guru.dashboard', 'guru.dokumen*'],
                'label' => 'Daftar Siswa',
                'kurikulum' => false,
                'badge' => 0,
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            ],
            [
                'route' => 'guru.surat.index',
                'match' => ['guru.surat.*'],
                'label' => 'Pengajuan Surat',
                'kurikulum' => true,
                'badge' => isset($suratBaru) ? $suratBaru->count() : 0,
                'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            ],
            [
                'route' => 'guru.peminjaman.index',
                'match' => ['guru.peminjaman.*'],
                'label' => 'Peminjaman Dokumen',
                'kurikulum' => true,
                'badge' => isset($peminjamanBaru) ? $peminjamanBaru->count() : 0,
                'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            ],
        ];
    @endphp

    <div class="mx-1 mb-4 flex items-center gap-3 rounded-xl bg-white/5 px-3 py-3 ring-1 ring-white/10">
        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 text-sm font-bold text-white"
            aria-hidden="true">{{ strtoupper(mb_substr($guruLogin->nama, 0, 1)) }}</span>
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-white">{{ $guruLogin->nama }}</p>
            <p class="truncate text-xs text-primary-200/80">
                {{ $guruLogin->label_role }}{{ $guruLogin->isWaliKelas() ? ' — ' . $guruLogin->kelas_dibina : '' }}
            </p>
        </div>
    </div>

    <nav class="space-y-1" aria-label="Menu guru">
        @foreach($menus as $m)
            @continue($m['kurikulum'] && !$guruLogin->isKurikulum())
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
                @if($m['badge'] > 0)
                    <span class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-amber-400 px-1.5 text-[11px] font-bold leading-none text-slate-900">
                        {{ $m['badge'] > 99 ? '99+' : $m['badge'] }}
                    </span>
                @endif
            </a>
        @endforeach
    </nav>
@endsection

@section('content')
    @php
        // ---- Data tampilan ----
        $targetDokumen = 11; // 6 raport + 1 ijazah + 4 sertifikat
        $difilter = request('q') || request('kelas');
        $totalSiswa = method_exists($siswas, 'total') ? $siswas->total() : $siswas->count();

        // Kolom tabel di layar lebar: siswa | NIS | kelas | kelengkapan | aksi
        $cols = 'lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,0.9fr)_minmax(0,1.5fr)_auto]';

        // ---- Gaya yang dipakai berulang ----
        $btn = 'inline-flex items-center justify-center gap-1.5 rounded-xl font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2';
        $btnPrimary = $btn . ' bg-blue-700 text-white shadow-sm hover:bg-blue-800 focus-visible:ring-blue-500';
        $btnOutline = $btn . ' border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus-visible:ring-slate-400';
        $inp = 'w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20';

        // Avatar inisial: warna ditentukan dari nama
        $avatar = function ($name, $size = 'h-10 w-10 text-sm') {
            $palette = ['bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700', 'bg-purple-100 text-purple-700', 'bg-amber-100 text-amber-800', 'bg-rose-100 text-rose-700', 'bg-sky-100 text-sky-700'];
            $color = $palette[abs(crc32((string) $name)) % count($palette)];
            return '<span class="inline-flex shrink-0 items-center justify-center rounded-full font-bold ' . $size . ' ' . $color . '" aria-hidden="true">'
                . e(strtoupper(mb_substr((string) $name, 0, 1))) . '</span>';
        };

        // Badge kecil
        $badge = function ($variant, $text) {
            $styles = [
                'blue' => 'bg-blue-50 text-blue-700 ring-blue-200',
                'purple' => 'bg-purple-50 text-purple-700 ring-purple-200',
            ];
            return '<span class="inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset '
                . ($styles[$variant] ?? $styles['blue']) . '">' . e($text) . '</span>';
        };

        // ---- Notifikasi pengajuan (khusus guru kurikulum) ----
        $peminjamanBaru = $peminjamanBaru ?? collect();
        $suratBaru = $suratBaru ?? collect();

        $urlSurat = route('guru.surat.index', ['status' => 'diajukan']);
        $urlPeminjaman = route('guru.peminjaman.index', ['status' => 'menunggu']);

        $daftar = collect();
        foreach ($suratBaru as $n) {
            $daftar->push([
                'jenis' => 'Surat Keterangan',
                'variant' => 'blue',
                'siswa' => $n->siswa->nama ?? '-',
                'ket' => $n->tujuan_pengajuan,
                'waktu' => $n->created_at,
                'url' => $urlSurat,
            ]);
        }
        foreach ($peminjamanBaru as $n) {
            $daftar->push([
                'jenis' => 'Peminjaman Dokumen',
                'variant' => 'purple',
                'siswa' => $n->siswa->nama ?? '-',
                'ket' => $n->dokumen . ' (ambil ' . $n->tanggal_ambil->translatedFormat('d M Y') . ')',
                'waktu' => $n->created_at,
                'url' => $urlPeminjaman,
            ]);
        }
        $daftar = $daftar->sortByDesc('waktu')->values();
        $totalNotif = $daftar->count();
        $tampilNotif = $daftar->take(8);
    @endphp

    {{-- PESAN FLASH --}}
    @if(session('success') || session('error') || $errors->any())
        <div class="mb-4 space-y-3" role="status">
            @foreach(['success'] as $key)
                @if(session($key))
                    <div data-flash class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        <p class="flex-1">{{ session($key) }}</p>
                        <button type="button" aria-label="Tutup pesan" onclick="this.closest('[data-flash]').remove()"
                            class="-m-1 rounded p-1 opacity-60 transition hover:opacity-100">&times;</button>
                    </div>
                @endif
            @endforeach
            @if(session('error'))
                <div data-flash class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="flex-1">{{ session('error') }}</p>
                    <button type="button" aria-label="Tutup pesan" onclick="this.closest('[data-flash]').remove()"
                        class="-m-1 rounded p-1 opacity-60 transition hover:opacity-100">&times;</button>
                </div>
            @endif
            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-inside list-disc space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    {{-- NOTIFIKASI PENGAJUAN: klik judul untuk melipat atau membuka --}}
    @if($guru->isKurikulum() && $totalNotif > 0)
        <details open class="group mb-4 overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm">
            <summary
                class="flex cursor-pointer list-none items-center gap-3 bg-amber-50 px-4 py-4 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-amber-500 sm:px-6 [&::-webkit-details-marker]:hidden">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="font-semibold text-amber-900">Pengajuan menunggu konfirmasi</h2>
                    <p class="text-sm text-amber-800/80">{{ $totalNotif }} pengajuan dari siswa perlu Anda tindak lanjuti.</p>
                </div>
                <svg class="h-5 w-5 shrink-0 text-amber-700 transition-transform group-open:rotate-180" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>

            <ul class="divide-y divide-slate-100">
                @foreach($tampilNotif as $n)
                    <li class="flex flex-col gap-2.5 px-4 py-3.5 sm:flex-row sm:items-center sm:gap-4 sm:px-6">
                        <div class="flex min-w-0 flex-1 items-center gap-3">
                            {!! $avatar($n['siswa'], 'h-8 w-8 text-xs') !!}
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    <p class="text-sm font-semibold text-slate-800">{{ $n['siswa'] }}</p>
                                    {!! $badge($n['variant'], $n['jenis']) !!}
                                </div>
                                <p class="mt-0.5 truncate text-sm text-slate-500" title="{{ $n['ket'] }}">{{ $n['ket'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-3 sm:justify-end">
                            <span class="whitespace-nowrap text-xs text-slate-400">{{ $n['waktu']->diffForHumans() }}</span>
                            <a href="{{ $n['url'] }}" class="{{ $btnPrimary }} px-3 py-2 text-xs">Lihat</a>
                        </div>
                    </li>
                @endforeach
            </ul>

            @if($totalNotif > $tampilNotif->count())
                <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 px-4 py-3 text-sm text-slate-500 sm:px-6">
                    <span>Menampilkan {{ $tampilNotif->count() }} dari {{ $totalNotif }} pengajuan.</span>
                    <span class="flex gap-4 font-semibold">
                        @if($suratBaru->isNotEmpty())
                            <a href="{{ $urlSurat }}" class="text-blue-700 hover:text-blue-900">Semua surat</a>
                        @endif
                        @if($peminjamanBaru->isNotEmpty())
                            <a href="{{ $urlPeminjaman }}" class="text-blue-700 hover:text-blue-900">Semua peminjaman</a>
                        @endif
                    </span>
                </div>
            @endif
        </details>
    @endif

    {{-- DAFTAR SISWA --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <header class="flex flex-col gap-3 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="min-w-0">
                @if($guru->isWaliKelas())
                    <h2 class="text-base font-bold text-slate-900">Siswa Kelas {{ $guru->kelas_dibina }}</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Sebagai wali kelas, Anda dapat mengunggah raport siswa binaan.</p>
                @else
                    <h2 class="text-base font-bold text-slate-900">Seluruh Siswa</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Sebagai guru kurikulum, Anda dapat mengunggah ijazah dan sertifikat untuk semua siswa.</p>
                @endif
            </div>
            <span class="inline-flex items-center self-start whitespace-nowrap rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-200 sm:self-auto">
                {{ $totalSiswa }} siswa
            </span>
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

                @if($guru->isKurikulum())
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
                @endif

                <div class="flex gap-2">
                    <button type="submit" class="{{ $btnPrimary }} flex-1 px-4 py-2.5 text-sm sm:flex-none">Cari</button>
                    @if($difilter)
                        <a href="{{ route('guru.dashboard') }}" class="{{ $btnOutline }} flex-1 px-4 py-2.5 text-sm sm:flex-none">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Kartu di HP/tablet, tabel di layar lebar --}}
        <div role="table" aria-label="Daftar siswa">
            <div role="row"
                class="hidden gap-4 border-b border-slate-100 bg-slate-50 px-6 py-3 text-sm font-semibold text-slate-500 lg:grid {{ $cols }}">
                <div role="columnheader">Siswa</div>
                <div role="columnheader">NIS</div>
                <div role="columnheader">Kelas</div>
                <div role="columnheader">Kelengkapan dokumen</div>
                <div role="columnheader" class="text-right">Aksi</div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($siswas as $s)
                    @php $pct = min(100, (int) round($s->dokumens_count / $targetDokumen * 100)); @endphp

                    <div role="row"
                        class="table-row-hover grid grid-cols-[minmax(0,1fr)_auto] items-center gap-x-3 gap-y-3 px-4 py-4 sm:px-6 lg:gap-4 {{ $cols }}">

                        <div role="cell" class="flex min-w-0 items-center gap-3">
                            {!! $avatar($s->nama) !!}
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $s->nama }}</p>
                                <p class="text-xs text-slate-500 lg:hidden">NIS {{ $s->nis }}</p>
                            </div>
                        </div>

                        <div role="cell" class="hidden text-sm text-slate-500 lg:block">{{ $s->nis }}</div>

                        {{-- Aksi: ikon saja di HP, dengan teks di layar lebar --}}
                        <div role="cell" class="lg:order-last">
                            <a href="{{ route('guru.dokumen', $s) }}" aria-label="Kelola dokumen {{ $s->nama }}"
                                class="{{ $btnPrimary }} px-3 py-2 text-xs">
                                <span class="hidden lg:inline">Kelola Dokumen</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        {{-- Kelas + kelengkapan: satu baris di HP, dua kolom di layar lebar --}}
                        <div class="col-span-2 flex items-center gap-4 lg:contents">
                            <div role="cell">
                                <span class="inline-flex items-center whitespace-nowrap rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-200">{{ $s->kelas }}</span>
                            </div>
                            <div role="cell" class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2 text-xs text-slate-500">
                                    <span>{{ $s->dokumens_count }} dari {{ $targetDokumen }} dokumen</span>
                                    <span class="font-semibold text-slate-700">{{ $pct }}%</span>
                                </div>
                                <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full {{ $pct >= 100 ? 'bg-emerald-500' : 'bg-blue-600' }}" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
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
                            <a href="{{ route('guru.dashboard') }}" class="{{ $btnOutline }} mt-4 px-3 py-2 text-xs">Hapus filter</a>
                        @else
                            <p class="mt-4 text-sm font-semibold text-slate-700">Belum ada siswa</p>
                            <p class="mt-1 max-w-xs text-sm text-slate-400">
                                {{ $guru->isWaliKelas() ? 'Belum ada siswa di kelas ini.' : 'Data siswa belum tersedia.' }}
                            </p>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        @if(method_exists($siswas, 'hasPages') && $siswas->hasPages())
            <div class="border-t border-slate-100 px-4 py-4 sm:px-6">{{ $siswas->withQueryString()->links() }}</div>
        @endif
    </section>

    <script>
        // Pesan sukses hilang sendiri setelah 6 detik
        document.querySelectorAll('[data-flash]').forEach(function (el) {
            if (!el.classList.contains('bg-emerald-50')) return;
            setTimeout(function () {
                el.style.transition = 'opacity .4s';
                el.style.opacity = '0';
                setTimeout(function () { el.remove(); }, 400);
            }, 6000);
        });
    </script>
@endsection