@extends('layouts.app')
@section('title', 'Dokumen Siswa')
@section('page-title', 'Kelola Dokumen Siswa')
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
        // ---- Pastikan relasi sudah dimuat (hindari query berulang / N+1) ----
        $siswa->loadMissing(['dokumens.guru', 'dokumens.admin', 'guru']);

        // ---- Data dokumen ----
        $raportBySemester = $siswa->dokumens->where('kategori', 'raport')->keyBy('semester');
        $ijazah = $siswa->dokumens->firstWhere('kategori', 'ijazah');
        $sertifikatByJenis = $siswa->dokumens->where('kategori', 'sertifikat')->keyBy('jenis_sertifikat');

        $raportCount = $raportBySemester->count();
        $sertifikatCount = $sertifikatByJenis->count();
        $totalDokumen = $raportCount + ($ijazah ? 1 : 0) + $sertifikatCount;
        $totalTarget = 6 + 1 + 4;
        $persenLengkap = $totalTarget > 0 ? (int) round(($totalDokumen / $totalTarget) * 100) : 0;
        $pctRaport = min(100, (int) round($raportCount / 6 * 100));
        $pctSertifikat = min(100, (int) round($sertifikatCount / 4 * 100));

        // Lingkaran progres
        $circ = round(2 * pi() * 34, 2);
        $dash = round($circ * $persenLengkap / 100, 2);

        // ---- Riwayat unggahan (10 terakhir, dilihat wali kelas & guru kurikulum) ----
        $riwayat = \App\Models\Dokumen::withTrashed()
            ->where('siswa_id', $siswa->id)
            ->with(['guru', 'admin'])
            ->latest()
            ->orderByDesc('id')
            ->take(10)
            ->get();

        // ---- Hak akses ----
        $bisaKelolaRaport = $guru->isWaliKelas();
        $bisaKelolaIjazahSertifikat = $guru->isKurikulum();

        // Kategori yang boleh diunggah oleh guru yang sedang login
        $kategoriOptions = [];
        if ($bisaKelolaRaport) {
            $kategoriOptions['raport'] = 'Raport';
        }
        if ($bisaKelolaIjazahSertifikat) {
            $kategoriOptions['ijazah'] = 'Ijazah';
            $kategoriOptions['sertifikat'] = 'Sertifikat';
        }
        $kategoriTunggal = count($kategoriOptions) === 1 ? array_key_first($kategoriOptions) : null;

        // ---- Gaya yang dipakai berulang ----
        $card = 'rounded-2xl border border-slate-200 bg-white shadow-sm';
        $cardHead = 'flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4';
        $chip = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl';
        $note = 'border-b border-slate-100 bg-slate-50 px-5 py-2.5 text-xs text-slate-500';
        $row = 'flex items-center justify-between gap-3 px-5 py-3';
        $addBtn = 'inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500';
        $belum = 'rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-xs font-medium text-slate-400';
        $inp = 'w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 disabled:bg-slate-50';
        $lbl = 'mb-1.5 block text-sm font-medium text-slate-800';
        $btn = 'inline-flex items-center justify-center gap-1.5 rounded-xl font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2';
        $btnPrimary = $btn . ' bg-blue-700 text-white shadow-sm hover:bg-blue-800 focus-visible:ring-blue-500';
        $btnOutline = $btn . ' border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus-visible:ring-slate-400';
        $btnOutlinePrimary = $btn . ' border border-blue-200 bg-white text-blue-700 hover:bg-blue-50 focus-visible:ring-blue-500';
        $pill = 'inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500';

        // ---- Ikon ----
        $iconPlus = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>';
        $iconEye = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>';
        $iconDownload = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" /></svg>';
        $iconTrash = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>';

        // Tombol lihat, unduh, dan (opsional) hapus untuk satu dokumen
        $aksiDokumen = function ($doc, $boleh = false) use ($pill, $iconEye, $iconDownload, $iconTrash) {
            $url = e(asset('storage/' . $doc->file_path));
            $html = '<div class="flex shrink-0 items-center gap-1.5">'
                . '<a href="' . $url . '" target="_blank" rel="noopener" title="Lihat" aria-label="Lihat dokumen" class="' . $pill . ' bg-slate-100 text-slate-600 hover:bg-slate-200">' . $iconEye . '</a>'
                . '<a href="' . $url . '" download="' . e($doc->nama_file) . '" title="Unduh" aria-label="Unduh dokumen" class="' . $pill . ' bg-blue-50 text-blue-600 hover:bg-blue-100">' . $iconDownload . '</a>';
            if ($boleh) {
                $html .= '<form method="POST" action="' . e(route('guru.dokumen.destroy', $doc)) . '" onsubmit="return confirm(\'Hapus dokumen ini?\')" class="inline">'
                    . csrf_field() . method_field('DELETE')
                    . '<button type="submit" title="Hapus" aria-label="Hapus dokumen" class="' . $pill . ' bg-red-50 text-red-600 hover:bg-red-100">' . $iconTrash . '</button>'
                    . '</form>';
            }
            return $html . '</div>';
        };

        // Avatar inisial: warna ditentukan dari nama
        $palette = ['bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700', 'bg-purple-100 text-purple-700', 'bg-amber-100 text-amber-800', 'bg-rose-100 text-rose-700', 'bg-sky-100 text-sky-700'];
        $warnaAvatar = $palette[abs(crc32((string) $siswa->nama)) % count($palette)];

        // Ikon & warna tiap kategori
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

        // Kolom riwayat di layar sedang ke atas: dokumen | pengunggah | waktu | aksi
        $colsRiwayat = 'sm:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)_minmax(0,0.8fr)_auto]';
    @endphp

    <style>
        /* Satu gerakan saat halaman dibuka: bar progres tumbuh dari kiri */
        @keyframes barGrow { from { transform: scaleX(0); } to { transform: scaleX(1); } }
        .bar-grow { transform-origin: left; animation: barGrow .8s cubic-bezier(.2, .7, .3, 1) both; }
        @keyframes modalIn { from { opacity: 0; transform: translateY(16px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .modal-panel { animation: modalIn .2s cubic-bezier(.2, .7, .3, 1) both; }
        @media (prefers-reduced-motion: reduce) { .bar-grow, .modal-panel { animation: none; } }
    </style>

    <div class="w-full space-y-4 sm:space-y-6">

        {{-- KEMBALI --}}
        <a href="{{ route('guru.dashboard') }}"
            class="inline-flex items-center gap-1.5 rounded-lg text-sm font-semibold text-blue-700 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Daftar Siswa
        </a>

        {{-- PESAN FLASH --}}
        @if(session('success') || session('error') || $errors->any())
            <div class="space-y-3" role="status">
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
                            @if($siswa->guru)
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">Wali: {{ $siswa->guru->nama }}</span>
                            @endif
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
                        <div class="bar-grow h-full rounded-full {{ $st['bar'] }}" style="width: {{ $st['pct'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- TIGA KATEGORI --}}
        <div class="grid grid-cols-1 items-stretch gap-4 sm:gap-6 md:grid-cols-2 xl:grid-cols-3">

            {{-- RAPORT --}}
            <section class="{{ $card }} flex min-w-0 flex-col overflow-hidden">
                <div class="{{ $cardHead }}">
                    <div class="flex min-w-0 items-center gap-3">
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
                    @if($bisaKelolaRaport)
                        <button type="button" onclick="openUploadModal('raport')" title="Unggah raport"
                            aria-label="Unggah raport" class="{{ $addBtn }}">{!! $iconPlus !!}</button>
                    @endif
                </div>

                @unless($bisaKelolaRaport)
                    <p class="{{ $note }}">Hanya dapat dikelola oleh wali kelas.</p>
                @endunless

                <div class="divide-y divide-slate-100">
                    @for($i = 1; $i <= 6; $i++)
                        @php $doc = $raportBySemester->get($i); @endphp
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
                                {!! $aksiDokumen($doc, $bisaKelolaRaport) !!}
                            @elseif($bisaKelolaRaport)
                                <button type="button" onclick="openUploadModal('raport', {{ $i }})"
                                    title="Unggah semester {{ $i }}" aria-label="Unggah raport semester {{ $i }}"
                                    class="{{ $addBtn }}">{!! $iconPlus !!}</button>
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
                    <div class="flex min-w-0 items-center gap-3">
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
                    @if($bisaKelolaIjazahSertifikat && !$ijazah)
                        <button type="button" onclick="openUploadModal('ijazah')" title="Unggah ijazah"
                            aria-label="Unggah ijazah" class="{{ $addBtn }}">{!! $iconPlus !!}</button>
                    @endif
                </div>

                @unless($bisaKelolaIjazahSertifikat)
                    <p class="{{ $note }}">Hanya dapat dikelola oleh guru kurikulum.</p>
                @endunless

                @if($ijazah)
                    <div class="{{ $row }}">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-slate-700" title="{{ $ijazah->nama_file }}">{{ $ijazah->nama_file }}</p>
                            <p class="text-xs text-slate-400">Diunggah {{ $ijazah->created_at->translatedFormat('d M Y') }}</p>
                        </div>
                        {!! $aksiDokumen($ijazah, $bisaKelolaIjazahSertifikat) !!}
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
                        @if($bisaKelolaIjazahSertifikat)
                            <p class="mt-1 max-w-[16rem] text-sm text-slate-400">Ijazah biasanya diunggah menjelang kelulusan siswa kelas 12.</p>
                            <button type="button" onclick="openUploadModal('ijazah')"
                                class="{{ $btnOutlinePrimary }} mt-4 px-3 py-2 text-xs">Unggah ijazah</button>
                        @endif
                    </div>
                @endif
            </section>

            {{-- SERTIFIKAT --}}
            <section class="{{ $card }} flex min-w-0 flex-col overflow-hidden md:col-span-2 xl:col-span-1">
                <div class="{{ $cardHead }}">
                    <div class="flex min-w-0 items-center gap-3">
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
                </div>

                @unless($bisaKelolaIjazahSertifikat)
                    <p class="{{ $note }}">Hanya dapat dikelola oleh guru kurikulum.</p>
                @endunless

                <div class="divide-y divide-slate-100">
                    @foreach(['TOEIC', 'TKA', 'UKK', 'PKL'] as $jenis)
                        @php $doc = $sertifikatByJenis->get($jenis); @endphp
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
                                {!! $aksiDokumen($doc, $bisaKelolaIjazahSertifikat) !!}
                            @elseif($bisaKelolaIjazahSertifikat)
                                <button type="button" onclick="openUploadModal('sertifikat', '{{ $jenis }}')"
                                    title="Unggah {{ $jenis }}" aria-label="Unggah sertifikat {{ $jenis }}"
                                    class="{{ $addBtn }}">{!! $iconPlus !!}</button>
                            @else
                                <span class="{{ $belum }}">Belum ada</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- RIWAYAT UNGGAHAN --}}
        <section class="{{ $card }} overflow-hidden">
            <div class="border-b border-slate-100 px-4 py-4 sm:px-6">
                <h3 class="text-base font-bold text-slate-900">Riwayat unggahan</h3>
                <p class="mt-0.5 text-sm text-slate-500">10 unggahan terakhir untuk siswa ini, termasuk versi lama yang sudah digantikan atau dihapus.</p>
            </div>

            @if($riwayat->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-sm font-semibold text-slate-700">Belum ada dokumen yang diunggah</p>
                    <p class="mt-1 text-sm text-slate-400">Dokumen baru akan tercatat di sini.</p>
                </div>
            @else
                <div role="table" aria-label="Riwayat unggahan">
                    <div role="row"
                        class="hidden gap-4 border-b border-slate-100 bg-slate-50 px-6 py-3 text-sm font-semibold text-slate-500 sm:grid {{ $colsRiwayat }}">
                        <div role="columnheader">Dokumen</div>
                        <div role="columnheader">Diunggah oleh</div>
                        <div role="columnheader">Waktu</div>
                        <div role="columnheader" class="text-right">Aksi</div>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($riwayat as $d)
                            <div role="row"
                                class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-x-3 gap-y-1.5 px-4 py-3.5 sm:gap-4 sm:px-6 {{ $colsRiwayat }}">

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
                                    @if($d->trashed())
                                        <span class="mt-1 inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500 ring-1 ring-inset ring-slate-200"
                                            title="Digantikan atau dihapus pada {{ $d->deleted_at->translatedFormat('d M Y H:i') }}">Versi lama</span>
                                    @endif
                                </div>

                                {{-- Aksi: kanan atas di HP, kolom terakhir di layar sedang ke atas --}}
                                <div role="cell" class="sm:order-last">
                                    <div class="flex justify-end">{!! $aksiDokumen($d, false) !!}</div>
                                </div>

                                {{-- Pengunggah --}}
                                <div role="cell" class="col-span-2 text-sm sm:col-span-1">
                                    <span class="text-slate-700">{{ $d->nama_pengunggah }}</span>
                                    @if($d->guru)
                                        <span class="text-slate-400 sm:block sm:text-xs"> {{ $d->guru->label_role }}</span>
                                    @endif
                                </div>

                                {{-- Waktu --}}
                                <div role="cell" class="col-span-2 text-sm text-slate-500 sm:col-span-1">
                                    {{ $d->created_at->translatedFormat('d M Y') }}
                                    <span class="text-slate-400 sm:block sm:text-xs">{{ $d->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    </div>

    {{-- MODAL UPLOAD --}}
    @if(count($kategoriOptions))
        <div id="uploadModal" role="dialog" aria-modal="true" aria-labelledby="uploadModalTitle"
            class="fixed inset-0 z-50 flex hidden items-end justify-center bg-slate-900/50 backdrop-blur-sm sm:items-center sm:p-4">
            <div class="modal-panel flex max-h-[92vh] w-full max-w-md flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl sm:rounded-2xl">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
                    <h3 id="uploadModalTitle" class="text-base font-bold text-slate-900 sm:text-lg">Unggah dokumen</h3>
                    <button type="button" onclick="closeUploadModal()" aria-label="Tutup"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto px-5 py-5 sm:px-6">
                    <form method="POST" action="{{ route('guru.dokumen.store', $siswa) }}" enctype="multipart/form-data"
                        id="uploadForm" class="space-y-4">
                        @csrf

                        {{-- Kategori: hidden bila hanya ada satu pilihan, dropdown bila lebih --}}
                        @if($kategoriTunggal)
                            <input type="hidden" name="kategori" value="{{ $kategoriTunggal }}">
                        @else
                            <div>
                                <label for="kategoriSelect" class="{{ $lbl }}">Kategori</label>
                                <select name="kategori" id="kategoriSelect" onchange="toggleFields()" required class="{{ $inp }}">
                                    <option value="">Pilih kategori</option>
                                    @foreach($kategoriOptions as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if($bisaKelolaRaport)
                            <div id="semesterField" class="{{ $kategoriTunggal === 'raport' ? '' : 'hidden' }}">
                                <label for="semesterSelect" class="{{ $lbl }}">Semester</label>
                                <select name="semester" id="semesterSelect" class="{{ $inp }}"
                                    {{ $kategoriTunggal === 'raport' ? 'required' : 'disabled' }}>
                                    @for($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}">Semester {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        @endif

                        @if($bisaKelolaIjazahSertifikat)
                            <div id="sertifikatField" class="hidden">
                                <label for="jenisSertifikatSelect" class="{{ $lbl }}">Jenis sertifikat</label>
                                <select name="jenis_sertifikat" id="jenisSertifikatSelect" disabled class="{{ $inp }}">
                                    <option value="TOEIC">TOEIC</option>
                                    <option value="TKA">TKA</option>
                                    <option value="UKK">UKK</option>
                                    <option value="PKL">PKL</option>
                                </select>
                            </div>
                        @endif

                        <div>
                            <span class="{{ $lbl }}">File</span>
                            <label id="dropzone" for="fileInput"
                                class="flex w-full cursor-pointer flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center transition hover:border-blue-400 hover:bg-blue-50/50 focus-within:border-blue-500">
                                <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span id="fileLabel" class="break-all text-sm font-medium text-slate-600">Klik atau seret file ke sini</span>
                                <span id="fileMeta" class="text-xs text-slate-400">PDF, JPG, atau PNG. Maksimal 5 MB.</span>
                            </label>
                            <input type="file" name="file" id="fileInput" required class="sr-only"
                                accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileChange(this)">
                        </div>

                        <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                            <button type="button" onclick="closeUploadModal()" class="{{ $btnOutline }} px-4 py-2.5 text-sm">Batal</button>
                            <button type="submit" class="{{ $btnPrimary }} px-4 py-2.5 text-sm">Unggah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            const kategoriTunggal = @json($kategoriTunggal);
            const MAX_SIZE = 5 * 1024 * 1024; // 5 MB
            const ALLOWED_EXT = ['pdf', 'jpg', 'jpeg', 'png'];
            const LABEL_DEFAULT = 'Klik atau seret file ke sini';
            const META_DEFAULT = 'PDF, JPG, atau PNG. Maksimal 5 MB.';

            const modal = document.getElementById('uploadModal');
            const kategoriSelect = document.getElementById('kategoriSelect');   // null jika hanya satu kategori
            const semesterField = document.getElementById('semesterField');    // null jika bukan wali kelas
            const semesterSelect = document.getElementById('semesterSelect');
            const sertifikatField = document.getElementById('sertifikatField'); // null jika bukan kurikulum
            const jenisSelect = document.getElementById('jenisSertifikatSelect');
            const fileInput = document.getElementById('fileInput');
            const fileLabel = document.getElementById('fileLabel');
            const fileMeta = document.getElementById('fileMeta');
            const dropzone = document.getElementById('dropzone');
            const uploadForm = document.getElementById('uploadForm');

            function kategoriAktif() {
                return kategoriSelect ? kategoriSelect.value : kategoriTunggal;
            }

            // Tampilkan hanya field yang relevan dan nonaktifkan yang disembunyikan
            // supaya nilainya tidak ikut terkirim ke server.
            function toggleFields() {
                const kat = kategoriAktif();

                if (semesterField) {
                    const tampil = kat === 'raport';
                    semesterField.classList.toggle('hidden', !tampil);
                    semesterSelect.disabled = !tampil;
                    semesterSelect.required = tampil;
                }

                if (sertifikatField) {
                    const tampil = kat === 'sertifikat';
                    sertifikatField.classList.toggle('hidden', !tampil);
                    jenisSelect.disabled = !tampil;
                    jenisSelect.required = tampil;
                }
            }

            function formatUkuran(bytes) {
                return bytes < 1024 * 1024
                    ? Math.max(1, Math.round(bytes / 1024)) + ' KB'
                    : (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            }

            function resetFile() {
                fileInput.value = '';
                fileLabel.textContent = LABEL_DEFAULT;
                fileMeta.textContent = META_DEFAULT;
                fileMeta.classList.remove('text-red-600');
            }

            function handleFileChange(input) {
                const file = input.files[0];
                if (!file) return resetFile();

                const ext = file.name.split('.').pop().toLowerCase();
                if (!ALLOWED_EXT.includes(ext)) {
                    resetFile();
                    fileMeta.textContent = 'Format tidak didukung. Gunakan PDF, JPG, atau PNG.';
                    fileMeta.classList.add('text-red-600');
                    return;
                }
                if (file.size > MAX_SIZE) {
                    resetFile();
                    fileMeta.textContent = 'Ukuran file melebihi 5 MB. Pilih file yang lebih kecil.';
                    fileMeta.classList.add('text-red-600');
                    return;
                }

                fileLabel.textContent = file.name;
                fileMeta.textContent = formatUkuran(file.size);
                fileMeta.classList.remove('text-red-600');
            }

            function openUploadModal(kategori = '', extra = null) {
                resetFile();

                if (kategoriSelect) kategoriSelect.value = kategori;
                toggleFields();

                if (semesterSelect) semesterSelect.value = (kategori === 'raport' && extra) ? extra : 1;
                if (jenisSelect) jenisSelect.value = (kategori === 'sertifikat' && extra) ? extra : 'TOEIC';

                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeUploadModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            // Tutup saat klik area gelap atau tekan Esc
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeUploadModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeUploadModal();
            });

            // Seret & lepas file
            ['dragenter', 'dragover'].forEach(ev => dropzone.addEventListener(ev, (e) => {
                e.preventDefault();
                dropzone.classList.add('border-blue-500', 'bg-blue-50');
            }));
            ['dragleave', 'drop'].forEach(ev => dropzone.addEventListener(ev, (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-blue-500', 'bg-blue-50');
            }));
            dropzone.addEventListener('drop', (e) => {
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    handleFileChange(fileInput);
                }
            });

            // Cegah unggah ganda akibat klik berkali-kali
            uploadForm.addEventListener('submit', function (e) {
                if (this.dataset.submitting === '1') {
                    e.preventDefault();
                    return;
                }
                this.dataset.submitting = '1';
                const btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.textContent = 'Mengunggah...';
            });

            // Pulihkan tombol jika halaman dimuat dari cache (tombol Back browser)
            window.addEventListener('pageshow', function () {
                uploadForm.dataset.submitting = '0';
                const btn = uploadForm.querySelector('button[type="submit"]');
                btn.disabled = false;
                btn.textContent = 'Unggah';
            });

            toggleFields();
        </script>
    @endif
@endsection