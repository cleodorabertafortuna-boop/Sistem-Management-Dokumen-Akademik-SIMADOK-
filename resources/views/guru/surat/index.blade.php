@extends('layouts.app')
@section('title', 'Pengajuan Surat')
@section('page-title', 'Pengajuan Surat Keterangan')
@section('user-initial', $guru->nama ?? 'G')

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
        // Filter status (tab) dan warna badge
        $filters = ['' => 'Semua', 'diajukan' => 'Menunggu diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'];
        $statusStyle = [
            'diajukan' => ['bg-amber-50 text-amber-800 ring-amber-200', 'bg-amber-500'],
            'selesai' => ['bg-emerald-50 text-emerald-700 ring-emerald-200', 'bg-emerald-500'],
            'ditolak' => ['bg-red-50 text-red-700 ring-red-200', 'bg-red-500'],
        ];
        $statusAktif = (string) request('status', '');

        // Kolom tabel di layar lebar: siswa | tujuan | tanggal | status | aksi
        $cols = 'lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1.5fr)_minmax(0,0.9fr)_minmax(0,0.9fr)_minmax(0,1.2fr)]';

        // Gaya berulang
        $btn = 'inline-flex items-center justify-center gap-1.5 rounded-xl font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2';
        $btnPrimary = $btn . ' bg-blue-700 text-white shadow-sm hover:bg-blue-800 focus-visible:ring-blue-500';
        $btnDanger = $btn . ' bg-red-600 text-white shadow-sm hover:bg-red-700 focus-visible:ring-red-500';
        $btnOutline = $btn . ' border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus-visible:ring-slate-400';
        $btnOutlinePrimary = $btn . ' border border-blue-200 bg-white text-blue-700 hover:bg-blue-50 focus-visible:ring-blue-500';
        $btnOutlineDanger = $btn . ' border border-red-200 bg-white text-red-600 hover:bg-red-50 focus-visible:ring-red-500';
        $inp = 'w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20';
        $lbl = 'mb-1.5 block text-sm font-medium text-slate-800';

        // Avatar inisial: warna ditentukan dari nama
        $avatar = function ($name) {
            $palette = ['bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700', 'bg-purple-100 text-purple-700', 'bg-amber-100 text-amber-800', 'bg-rose-100 text-rose-700', 'bg-sky-100 text-sky-700'];
            $color = $palette[abs(crc32((string) $name)) % count($palette)];
            return '<span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold ' . $color . '" aria-hidden="true">'
                . e(strtoupper(mb_substr((string) $name, 0, 1))) . '</span>';
        };
    @endphp

    <style>
        @keyframes modalIn { from { opacity: 0; transform: translateY(16px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .modal-panel { animation: modalIn .2s cubic-bezier(.2, .7, .3, 1) both; }
        @media (prefers-reduced-motion: reduce) { .modal-panel { animation: none; } }
    </style>

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

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Judul + filter status --}}
        <header class="space-y-4 border-b border-slate-100 px-4 py-4 sm:px-6">
            <div>
                <h2 class="text-base font-bold text-slate-900">Pengajuan surat keterangan</h2>
                <p class="mt-0.5 text-sm text-slate-500">Proses atau tolak pengajuan siswa, lalu cetak surat yang sudah selesai.</p>
            </div>

            <nav aria-label="Filter status"
                class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-0.5 sm:mx-0 sm:flex-wrap sm:px-0 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                @foreach($filters as $value => $label)
                    @php $aktif = $statusAktif === (string) $value; @endphp
                    <a href="{{ request()->fullUrlWithQuery(['status' => $value === '' ? null : $value, 'page' => null]) }}"
                        @if($aktif) aria-current="page" @endif
                        class="shrink-0 rounded-full px-3.5 py-1.5 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2
                            {{ $aktif ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </header>

        <div role="table" aria-label="Pengajuan surat keterangan">
            <div role="row"
                class="hidden gap-4 border-b border-slate-100 bg-slate-50 px-6 py-3 text-sm font-semibold text-slate-500 lg:grid {{ $cols }}">
                <div role="columnheader">Siswa</div>
                <div role="columnheader">Tujuan</div>
                <div role="columnheader">Tanggal ajukan</div>
                <div role="columnheader">Status</div>
                <div role="columnheader" class="text-right">Aksi</div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($surats as $s)
                    @php [$badgeBox, $badgeDot] = $statusStyle[$s->status] ?? ['bg-slate-100 text-slate-600 ring-slate-200', 'bg-slate-400']; @endphp

                    <div role="row" class="table-row-hover grid gap-3 px-4 py-4 sm:px-6 lg:items-center lg:gap-4 {{ $cols }}">

                        {{-- Siswa + status: satu baris di HP, kolom terpisah di layar lebar --}}
                        <div class="flex items-start justify-between gap-3 lg:contents">
                            <div role="cell" class="flex min-w-0 items-center gap-3 lg:order-1">
                                {!! $avatar($s->siswa->nama) !!}
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ $s->siswa->nama }}</p>
                                    <p class="text-xs text-slate-500">{{ $s->siswa->nis }} &middot; {{ $s->siswa->kelas }}</p>
                                </div>
                            </div>

                            <div role="cell" class="lg:order-4">
                                <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $badgeBox }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $badgeDot }}"></span>
                                    {{ $s->label_status }}
                                </span>
                            </div>
                        </div>

                        {{-- Tujuan --}}
                        <div role="cell" class="min-w-0 text-sm lg:order-2">
                            <p class="text-xs text-slate-400 lg:hidden">Tujuan</p>
                            <p class="break-words text-slate-600">{{ $s->tujuan_pengajuan }}</p>
                        </div>

                        {{-- Tanggal --}}
                        <div role="cell" class="text-sm text-slate-500 lg:order-3">
                            <span class="text-xs text-slate-400 lg:hidden">Diajukan </span>{{ $s->created_at->translatedFormat('d M Y') }}
                        </div>

                        {{-- Aksi --}}
                        <div role="cell" class="flex flex-wrap gap-2 lg:order-5 lg:justify-end">
                            @if($s->status === 'diajukan')
                                <button type="button" data-id="{{ $s->id }}" data-nama="{{ $s->siswa->nama }}"
                                    onclick="openProsesModal(this)"
                                    class="{{ $btnPrimary }} flex-1 px-3 py-2 text-xs lg:flex-none">Proses</button>
                                <button type="button" data-id="{{ $s->id }}" data-nama="{{ $s->siswa->nama }}"
                                    onclick="openTolakModal(this)"
                                    class="{{ $btnOutlineDanger }} flex-1 px-3 py-2 text-xs lg:flex-none">Tolak</button>
                            @elseif($s->status === 'selesai')
                                <a href="{{ route('guru.surat.cetak', $s) }}" target="_blank" rel="noopener"
                                    class="{{ $btnOutlinePrimary }} flex-1 px-3 py-2 text-xs lg:flex-none">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Cetak PDF
                                </a>
                            @else
                                <span class="text-sm text-slate-300">&mdash;</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center px-6 py-14 text-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        @if($statusAktif !== '')
                            <p class="mt-4 text-sm font-semibold text-slate-700">Tidak ada pengajuan dengan status ini</p>
                            <p class="mt-1 max-w-xs text-sm text-slate-400">Pilih status lain untuk melihat pengajuan.</p>
                            <a href="{{ route('guru.surat.index') }}" class="{{ $btnOutline }} mt-4 px-3 py-2 text-xs">Lihat semua</a>
                        @else
                            <p class="mt-4 text-sm font-semibold text-slate-700">Belum ada pengajuan surat</p>
                            <p class="mt-1 max-w-xs text-sm text-slate-400">Pengajuan dari siswa akan muncul di sini.</p>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        @if(method_exists($surats, 'hasPages') && $surats->hasPages())
            <div class="border-t border-slate-100 px-4 py-4 sm:px-6">{{ $surats->withQueryString()->links() }}</div>
        @endif
    </section>

    {{-- MODAL PROSES --}}
    <div id="prosesModal" data-modal role="dialog" aria-modal="true" aria-labelledby="prosesModalTitle"
        class="fixed inset-0 z-50 flex hidden items-end justify-center bg-slate-900/50 backdrop-blur-sm sm:items-center sm:p-4">
        <div class="modal-panel flex max-h-[92vh] w-full max-w-md flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl sm:rounded-2xl">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
                <h3 id="prosesModalTitle" class="text-base font-bold text-slate-900 sm:text-lg">Proses surat keterangan</h3>
                <button type="button" onclick="closeModals()" aria-label="Tutup"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="overflow-y-auto px-5 py-5 sm:px-6">
                <form id="prosesForm" method="POST" class="space-y-4" data-once>
                    @csrf
                    <p class="text-sm text-slate-500">Surat untuk <span id="prosesNama" class="font-semibold text-slate-800"></span></p>
                    <div>
                        <label for="nomorSurat" class="{{ $lbl }}">Nomor surat</label>
                        <input id="nomorSurat" type="text" name="nomor_surat" placeholder="Contoh: 421.5/045/2026" required class="{{ $inp }}">
                    </div>
                    <div>
                        <label for="tanggalSurat" class="{{ $lbl }}">Tanggal surat</label>
                        <input id="tanggalSurat" type="date" name="tanggal_surat" required value="{{ date('Y-m-d') }}" class="{{ $inp }}">
                    </div>
                    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                        <button type="button" onclick="closeModals()" class="{{ $btnOutline }} px-4 py-2.5 text-sm">Batal</button>
                        <button type="submit" class="{{ $btnPrimary }} px-4 py-2.5 text-sm">Simpan dan selesaikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL TOLAK --}}
    <div id="tolakModal" data-modal role="dialog" aria-modal="true" aria-labelledby="tolakModalTitle"
        class="fixed inset-0 z-50 flex hidden items-end justify-center bg-slate-900/50 backdrop-blur-sm sm:items-center sm:p-4">
        <div class="modal-panel flex max-h-[92vh] w-full max-w-md flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl sm:rounded-2xl">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
                <h3 id="tolakModalTitle" class="text-base font-bold text-slate-900 sm:text-lg">Tolak pengajuan</h3>
                <button type="button" onclick="closeModals()" aria-label="Tutup"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="overflow-y-auto px-5 py-5 sm:px-6">
                <form id="tolakForm" method="POST" class="space-y-4" data-once>
                    @csrf
                    <p class="text-sm text-slate-500">Pengajuan dari <span id="tolakNama" class="font-semibold text-slate-800"></span></p>
                    <div>
                        <label for="alasanTolak" class="{{ $lbl }}">Alasan penolakan</label>
                        <textarea id="alasanTolak" name="catatan" rows="3" required
                            placeholder="Jelaskan alasan agar siswa tahu apa yang perlu diperbaiki" class="{{ $inp }}"></textarea>
                    </div>
                    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                        <button type="button" onclick="closeModals()" class="{{ $btnOutline }} px-4 py-2.5 text-sm">Batal</button>
                        <button type="submit" class="{{ $btnDanger }} px-4 py-2.5 text-sm">Tolak pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeModals() {
            document.querySelectorAll('[data-modal]').forEach(m => m.classList.add('hidden'));
            document.body.classList.remove('overflow-hidden');
        }

        function openProsesModal(btn) {
            document.getElementById('prosesForm').action = `/guru/surat/${btn.dataset.id}/proses`;
            document.getElementById('prosesNama').textContent = btn.dataset.nama;
            showModal('prosesModal');
            document.getElementById('nomorSurat').focus();
        }

        function openTolakModal(btn) {
            document.getElementById('tolakForm').action = `/guru/surat/${btn.dataset.id}/tolak`;
            document.getElementById('tolakNama').textContent = btn.dataset.nama;
            document.getElementById('alasanTolak').value = '';
            showModal('tolakModal');
            document.getElementById('alasanTolak').focus();
        }

        // Tutup saat klik area gelap atau tekan Esc
        document.querySelectorAll('[data-modal]').forEach(m => m.addEventListener('click', (e) => {
            if (e.target === m) closeModals();
        }));
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModals();
        });

        // Cegah kirim ganda
        document.querySelectorAll('form[data-once]').forEach(f => f.addEventListener('submit', function (e) {
            if (this.dataset.busy === '1') { e.preventDefault(); return; }
            this.dataset.busy = '1';
            const b = this.querySelector('[type=submit]');
            b.dataset.label = b.textContent;
            b.disabled = true;
            b.textContent = 'Memproses...';
        }));

        // Pulihkan tombol jika halaman dimuat dari cache (tombol Back browser)
        window.addEventListener('pageshow', () => {
            document.querySelectorAll('form[data-once]').forEach(f => {
                f.dataset.busy = '0';
                const b = f.querySelector('[type=submit]');
                b.disabled = false;
                if (b.dataset.label) b.textContent = b.dataset.label;
            });
        });

        // Pesan sukses hilang sendiri setelah 6 detik
        document.querySelectorAll('[data-flash].bg-emerald-50').forEach(el => setTimeout(() => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, 6000));
    </script>
@endsection