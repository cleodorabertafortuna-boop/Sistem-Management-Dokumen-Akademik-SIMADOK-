@extends('layouts.app')
@section('title', 'Detail Siswa')
@section('page-title', 'Detail & Dokumen Siswa')
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
        // ---- Pastikan relasi sudah dimuat (hindari query berulang / N+1) ----
        $siswa->loadMissing(['dokumens.guru', 'dokumens.admin', 'guru']);

        // ---- Data dokumen ----
        $raports = $siswa->dokumens->where('kategori', 'raport')->sortBy('semester')->keyBy('semester');
        $ijazah = $siswa->dokumens->where('kategori', 'ijazah')->first();
        $sertifikats = $siswa->dokumens->where('kategori', 'sertifikat')->keyBy('jenis_sertifikat');
        $riwayat = $siswa->dokumens->sortByDesc('created_at');

        $raportCount = $raports->count();
        $sertifikatCount = $sertifikats->count();
        $totalDokumen = $siswa->dokumens->count();
        $totalTarget = 6 + 1 + 4;
        $persenLengkap = min(100, (int) round(($totalDokumen / $totalTarget) * 100));
        $pctRaport = min(100, (int) round($raportCount / 6 * 100));
        $pctSertifikat = min(100, (int) round($sertifikatCount / 4 * 100));

        // Lingkaran progres
        $circ = round(2 * pi() * 34, 2);
        $dash = round($circ * $persenLengkap / 100, 2);

        // Avatar inisial: warna ditentukan dari nama
        $palette = ['bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700', 'bg-purple-100 text-purple-700', 'bg-amber-100 text-amber-800', 'bg-rose-100 text-rose-700', 'bg-sky-100 text-sky-700'];
        $warnaAvatar = $palette[abs(crc32((string) $siswa->nama)) % count($palette)];

        // ---- Gaya yang dipakai berulang ----
        $card = 'rounded-2xl border border-slate-200 bg-white shadow-sm';
        $cardHead = 'flex items-center gap-3 border-b border-slate-100 px-5 py-4';
        $chip = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl';
        $row = 'flex items-center justify-between gap-3 px-5 py-3';
        $belum = 'rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-xs font-medium text-slate-400';
        $pill = 'inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500';
        $btn = 'inline-flex items-center justify-center gap-1.5 rounded-xl font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2';
        $btnOutline = $btn . ' border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus-visible:ring-slate-400';

        // ---- Ikon ----
        $iconEye = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>';
        $iconDownload = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" /></svg>';

        // Tombol lihat dan unduh untuk satu dokumen (admin hanya melihat)
        $aksiDokumen = function ($doc) use ($pill, $iconEye, $iconDownload) {
            $url = e(asset('storage/' . $doc->file_path));
            return '<div class="flex shrink-0 items-center gap-1.5">'
                . '<a href="' . $url . '" target="_blank" rel="noopener" title="Lihat" aria-label="Lihat dokumen" class="' . $pill . ' bg-slate-100 text-slate-600 hover:bg-slate-200">' . $iconEye . '</a>'
                . '<a href="' . $url . '" download="' . e($doc->nama_file) . '" title="Unduh" aria-label="Unduh dokumen" class="' . $pill . ' bg-blue-50 text-blue-600 hover:bg-blue-100">' . $iconDownload . '</a>'
                . '</div>';
        };

        // Ikon tiap kategori
        $icoRaport = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
        $icoIjazah = 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0121 19H3a12.083 12.083 0 012.84-8.422L12 14z';
        $icoSertif = 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';

        // Kartu statistik
        $stats = [
            ['label' => 'Total dokumen', 'value' => $totalDokumen, 'suffix' => ' / ' . $totalTarget, 'pct' => $persenLengkap, 'bar' => 'bg-blue-600', 'chip' => 'bg-blue-50 text-blue-700', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'valueClass' => 'text-slate-900'],
            ['label' => 'Raport', 'value' => $raportCount, 'suffix' => ' / 6', 'pct' => $pctRaport, 'bar' => 'bg-blue-600', 'chip' => 'bg-blue-50 text-blue-700', 'icon' => $icoRaport, 'valueClass' => 'text-slate-900'],
            ['label' => 'Ijazah', 'value' => $ijazah ? 'Ada' : 'Belum', 'suffix' => '', 'pct' => $ijazah ? 100 : 0, 'bar' => 'bg-emerald-500', 'chip' => 'bg-emerald-50 text-emerald-700', 'icon' => $icoIjazah, 'valueClass' => $ijazah ? 'text-emerald-600' : 'text-slate-400'],
            ['label' => 'Sertifikat', 'value' => $sertifikatCount, 'suffix' => ' / 4', 'pct' => $pctSertifikat, 'bar' => 'bg-purple-500', 'chip' => 'bg-purple-50 text-purple-700', 'icon' => $icoSertif, 'valueClass' => 'text-slate-900'],
        ];

        // Kolom riwayat di layar sedang ke atas: dokumen | file | pengunggah | waktu | aksi
        $colsRiwayat = 'lg:grid-cols-[minmax(0,1.1fr)_minmax(0,1.5fr)_minmax(0,1fr)_minmax(0,0.8fr)_auto]';
    @endphp

    <div class="w-full space-y-4 sm:space-y-6">

        {{-- KEMBALI --}}
        <a href="{{ route('admin.siswa.index') }}"
            class="inline-flex items-center gap-1.5 rounded-lg text-sm font-semibold text-blue-700 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Data Siswa
        </a>

        {{-- INFO SISWA + KELENGKAPAN --}}
        <section class="{{ $card }} p-4 sm:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex min-w-0 items-center gap-4">
                    <span class="inline-flex h-14 w-14 shrink-0 items-center justify-center rounded-full text-xl font-bold {{ $warnaAvatar }}"
                        aria-hidden="true">{{ strtoupper(mb_substr($siswa->nama, 0, 1)) }}</span>
                    <div class="min-w-0">
                        <h2 class="truncate text-xl font-bold leading-tight text-slate-900">{{ $siswa->nama }}</h2>
                        <p class="mt-0.5 text-sm text-slate-500">NIS {{ $siswa->nis }}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-200">Kelas {{ $siswa->kelas }}</span>
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">Wali: {{ $siswa->guru->nama ?? 'Belum ada wali kelas' }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-slate-100 lg:min-w-[18rem]">
                    <div class="relative h-16 w-16 shrink-0">
                        <svg viewBox="0 0 80 80" class="h-16 w-16 -rotate-90" aria-hidden="true">
                            <circle cx="40" cy="40" r="34" fill="none" stroke-width="8" class="stroke-slate-200" />
                            <circle cx="40" cy="40" r="34" fill="none" stroke-width="8" stroke-linecap="round"
                                class="{{ $persenLengkap >= 100 ? 'stroke-emerald-500' : 'stroke-blue-600' }}"
                                stroke-dasharray="{{ $dash }} {{ $circ }}" />
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-slate-800">{{ $persenLengkap }}%</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Kelengkapan dokumen</p>
                        <p class="mt-0.5 text-sm text-slate-500">{{ $totalDokumen }} dari {{ $totalTarget }} dokumen tersimpan</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- STATISTIK --}}
        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
            @foreach($stats as $st)
                <div class="{{ $card }} px-4 py-4 sm:px-5">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm text-slate-500">{{ $st['label'] }}</p>
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $st['chip'] }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $st['icon'] }}" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-1 text-2xl font-bold {{ $st['valueClass'] }}">{{ $st['value'] }}<span
                            class="text-base font-medium text-slate-400">{{ $st['suffix'] }}</span></p>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full {{ $st['bar'] }}" style="width: {{ $st['pct'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- TIGA KATEGORI --}}
        <div class="grid grid-cols-1 items-stretch gap-4 sm:gap-6 md:grid-cols-2 xl:grid-cols-3">

            {{-- RAPORT --}}
            <section class="{{ $card }} flex min-w-0 flex-col overflow-hidden">
                <div class="{{ $cardHead }}">
                    <div class="{{ $chip }} bg-blue-50 text-blue-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icoRaport }}" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-slate-900">Raport</h3>
                        <p class="text-xs text-slate-500">{{ $raportCount }} dari 6 semester</p>
                    </div>
                </div>
                <div class="divide-y divide-slate-100">
                    @for($i = 1; $i <= 6; $i++)
                        @php $doc = $raports->get($i); @endphp
                        <div class="{{ $row }}">
                            <div class="flex min-w-0 items-center gap-2.5">
                                <span class="h-2 w-2 shrink-0 rounded-full {{ $doc ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-700">Semester {{ $i }}</p>
                                    @if($doc)
                                        <p class="text-xs text-slate-400">Diunggah {{ $doc->created_at->translatedFormat('d M Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($doc)
                                {!! $aksiDokumen($doc) !!}
                            @else
                                <span class="{{ $belum }}">Belum ada</span>
                            @endif
                        </div>
                    @endfor
                </div>
            </section>

            {{-- IJAZAH --}}
            <section class="{{ $card }} flex min-w-0 flex-col overflow-hidden">
                <div class="{{ $cardHead }}">
                    <div class="{{ $chip }} bg-emerald-50 text-emerald-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icoIjazah }}" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-slate-900">Ijazah</h3>
                        <p class="text-xs text-slate-500">{{ $ijazah ? 'Sudah diunggah' : 'Belum tersedia' }}</p>
                    </div>
                </div>

                @if($ijazah)
                    <div class="{{ $row }}">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-slate-700" title="{{ $ijazah->nama_file }}">{{ $ijazah->nama_file }}</p>
                            <p class="text-xs text-slate-400">Diunggah {{ $ijazah->created_at->translatedFormat('d M Y') }}</p>
                        </div>
                        {!! $aksiDokumen($ijazah) !!}
                    </div>
                @else
                    <div class="flex flex-1 flex-col items-center justify-center px-5 py-10 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-slate-700">Ijazah belum tersedia</p>
                        <p class="mt-1 max-w-[16rem] text-sm text-slate-400">Ijazah diunggah oleh guru kurikulum menjelang kelulusan.</p>
                    </div>
                @endif
            </section>

            {{-- SERTIFIKAT --}}
            <section class="{{ $card }} flex min-w-0 flex-col overflow-hidden md:col-span-2 xl:col-span-1">
                <div class="{{ $cardHead }}">
                    <div class="{{ $chip }} bg-purple-50 text-purple-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icoSertif }}" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-slate-900">Sertifikat</h3>
                        <p class="text-xs text-slate-500">{{ $sertifikatCount }} dari 4 jenis</p>
                    </div>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach(['TOEIC', 'TKA', 'UKK', 'PKL'] as $jenis)
                        @php $doc = $sertifikats->get($jenis); @endphp
                        <div class="{{ $row }}">
                            <div class="flex min-w-0 items-center gap-2.5">
                                <span class="h-2 w-2 shrink-0 rounded-full {{ $doc ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-700">{{ $jenis }}</p>
                                    @if($doc)
                                        <p class="text-xs text-slate-400">Diunggah {{ $doc->created_at->translatedFormat('d M Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($doc)
                                {!! $aksiDokumen($doc) !!}
                            @else
                                <span class="{{ $belum }}">Belum ada</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- RIWAYAT SEMUA DOKUMEN --}}
        <section class="{{ $card }} overflow-hidden">
            <div class="border-b border-slate-100 px-4 py-4 sm:px-6">
                <h3 class="text-base font-bold text-slate-900">Riwayat semua dokumen</h3>
                <p class="mt-0.5 text-sm text-slate-500">Diurutkan dari yang paling baru diunggah.</p>
            </div>

            @if($riwayat->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-sm font-semibold text-slate-700">Belum ada dokumen</p>
                    <p class="mt-1 text-sm text-slate-400">Siswa ini belum memiliki dokumen apa pun.</p>
                </div>
            @else
                <div role="table" aria-label="Riwayat dokumen">
                    <div role="row"
                        class="hidden gap-4 border-b border-slate-100 bg-slate-50 px-6 py-3 text-sm font-semibold text-slate-500 lg:grid {{ $colsRiwayat }}">
                        <div role="columnheader">Dokumen</div>
                        <div role="columnheader">Nama file</div>
                        <div role="columnheader">Diunggah oleh</div>
                        <div role="columnheader">Tanggal</div>
                        <div role="columnheader" class="text-right">Aksi</div>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($riwayat as $d)
                            <div role="row"
                                class="table-row-hover grid grid-cols-[minmax(0,1fr)_auto] items-center gap-x-3 gap-y-1.5 px-4 py-3.5 sm:px-6 lg:gap-4 {{ $colsRiwayat }}">

                                {{-- Dokumen --}}
                                <div role="cell" class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800">
                                        <span class="capitalize">{{ $d->kategori }}</span>
                                        @if($d->kategori === 'raport')
                                            <span class="font-normal text-slate-500">Semester {{ $d->semester }}</span>
                                        @elseif($d->kategori === 'sertifikat')
                                            <span class="font-normal text-slate-500">{{ $d->jenis_sertifikat }}</span>
                                        @endif
                                    </p>
                                </div>

                                {{-- Aksi: kanan atas di HP, kolom terakhir di layar lebar --}}
                                <div role="cell" class="flex justify-end lg:order-last">{!! $aksiDokumen($d) !!}</div>

                                {{-- Nama file --}}
                                <div role="cell" class="col-span-2 min-w-0 truncate text-sm text-slate-600 lg:col-span-1" title="{{ $d->nama_file }}">{{ $d->nama_file }}</div>

                                {{-- Pengunggah --}}
                                <div role="cell" class="col-span-2 text-sm text-slate-500 lg:col-span-1">
                                    <span class="text-xs text-slate-400 lg:hidden">Oleh </span>{{ $d->nama_pengunggah }}
                                </div>

                                {{-- Tanggal --}}
                                <div role="cell" class="col-span-2 text-sm text-slate-500 lg:col-span-1">
                                    <span class="text-xs text-slate-400 lg:hidden">Diunggah </span>{{ $d->created_at->translatedFormat('d M Y') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    </div>
@endsection