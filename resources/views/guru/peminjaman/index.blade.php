@extends('layouts.app')
@section('title', 'Peminjaman Dokumen')
@section('page-title', 'Peminjaman Dokumen')
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
        // Filter status (tab) dan warna badge
        $filters = ['' => 'Semua', 'menunggu' => 'Menunggu konfirmasi', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'dikembalikan' => 'Sudah dikembalikan'];
        $statusStyle = [
            'menunggu' => ['bg-amber-50 text-amber-800 ring-amber-200', 'bg-amber-500'],
            'disetujui' => ['bg-blue-50 text-blue-700 ring-blue-200', 'bg-blue-500'],
            'ditolak' => ['bg-red-50 text-red-700 ring-red-200', 'bg-red-500'],
            'dikembalikan' => ['bg-emerald-50 text-emerald-700 ring-emerald-200', 'bg-emerald-500'],
        ];
        $statusAktif = (string) request('status', '');

        // Kolom tabel di layar lebar: siswa | dokumen | alasan | status | aksi
        $cols = 'lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1.1fr)_minmax(0,1.3fr)_minmax(0,1.1fr)_minmax(0,1.3fr)]';

        // Gaya berulang
        $btn = 'inline-flex items-center justify-center gap-1.5 rounded-xl font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2';
        $btnPrimary = $btn . ' bg-blue-700 text-white shadow-sm hover:bg-blue-800 focus-visible:ring-blue-500';
        $btnSuccess = $btn . ' bg-emerald-600 text-white shadow-sm hover:bg-emerald-700 focus-visible:ring-emerald-500';
        $btnDanger = $btn . ' bg-red-600 text-white shadow-sm hover:bg-red-700 focus-visible:ring-red-500';
        $btnOutline = $btn . ' border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus-visible:ring-slate-400';
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

    {{-- PESAN FLASH (key 'status' sudah ditampilkan oleh layout) --}}
    @if(session('success') || session('error'))
        <div class="mb-4 space-y-3" role="status">
            @if(session('success'))
                <div data-flash class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <p class="flex-1">{{ session('success') }}</p>
                    <button type="button" aria-label="Tutup pesan" onclick="this.closest('[data-flash]').remove()"
                        class="-m-1 rounded p-1 opacity-60 transition hover:opacity-100">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div data-flash class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="flex-1">{{ session('error') }}</p>
                    <button type="button" aria-label="Tutup pesan" onclick="this.closest('[data-flash]').remove()"
                        class="-m-1 rounded p-1 opacity-60 transition hover:opacity-100">&times;</button>
                </div>
            @endif
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Judul + filter status --}}
        <header class="space-y-4 border-b border-slate-100 px-4 py-4 sm:px-6">
            <div>
                <h2 class="text-base font-bold text-slate-900">Peminjaman dokumen</h2>
                <p class="mt-0.5 text-sm text-slate-500">Konfirmasi pengajuan siswa dan catat saat dokumen dikembalikan.</p>
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

        <div role="table" aria-label="Peminjaman dokumen">
            <div role="row"
                class="hidden gap-4 border-b border-slate-100 bg-slate-50 px-6 py-3 text-sm font-semibold text-slate-500 lg:grid {{ $cols }}">
                <div role="columnheader">Siswa</div>
                <div role="columnheader">Dokumen</div>
                <div role="columnheader">Alasan</div>
                <div role="columnheader">Status</div>
                <div role="columnheader" class="text-right">Aksi</div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($peminjamans as $p)
                    @php [$badgeBox, $badgeDot] = $statusStyle[$p->status] ?? ['bg-slate-100 text-slate-600 ring-slate-200', 'bg-slate-400']; @endphp

                    <div role="row" class="table-row-hover grid gap-3 px-4 py-4 sm:px-6 lg:items-start lg:gap-4 {{ $cols }}">

                        {{-- Siswa + status: satu baris di HP, kolom terpisah di layar lebar --}}
                        <div class="flex items-start justify-between gap-3 lg:contents">
                            <div role="cell" class="flex min-w-0 items-center gap-3 lg:order-1">
                                {!! $avatar($p->siswa->nama ?? '-') !!}
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ $p->siswa->nama ?? '-' }}</p>
                                    <p class="text-xs text-slate-500">{{ $p->siswa->kelas ?? '' }} &middot; NIS {{ $p->siswa->nis ?? '' }}</p>
                                </div>
                            </div>

                            <div role="cell" class="lg:order-4">
                                <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $badgeBox }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $badgeDot }}"></span>
                                    {{ $p->label_status }}
                                </span>
                                @if($p->status === 'dikembalikan' && $p->dikembalikan_at)
                                    <p class="mt-1.5 hidden text-xs text-slate-400 lg:block">{{ $p->dikembalikan_at->translatedFormat('d M Y, H:i') }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Dokumen + tanggal ambil --}}
                        <div role="cell" class="min-w-0 text-sm lg:order-2">
                            <p class="text-xs text-slate-400 lg:hidden">Dokumen</p>
                            <p class="break-words font-medium text-slate-700">{{ $p->dokumen }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">Diambil {{ $p->tanggal_ambil->translatedFormat('d M Y') }}</p>
                        </div>

                        {{-- Alasan --}}
                        <div role="cell" class="min-w-0 text-sm lg:order-3">
                            <p class="text-xs text-slate-400 lg:hidden">Alasan</p>
                            <p class="break-words text-slate-500">{{ $p->alasan }}</p>

                            @if($p->status === 'ditolak' && $p->catatan_guru)
                                <p class="mt-2 break-words rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700">
                                    <span class="font-semibold">Catatan penolakan:</span> {{ $p->catatan_guru }}
                                </p>
                            @endif

                            @if($p->status === 'dikembalikan' && $p->dikembalikan_at)
                                <p class="mt-2 text-xs text-slate-400 lg:hidden">Dikembalikan {{ $p->dikembalikan_at->translatedFormat('d M Y, H:i') }}</p>
                            @endif
                        </div>

                        {{-- Aksi --}}
                        <div role="cell" class="lg:order-5">
                            @if($p->status === 'menunggu')
                                <div class="flex flex-wrap gap-2 lg:justify-end">
                                    <form method="POST" action="{{ route('guru.peminjaman.setujui', $p) }}" class="flex-1 lg:flex-none" data-once>
                                        @csrf @method('PATCH')
                                        <button type="submit" class="{{ $btnPrimary }} w-full px-3 py-2 text-xs">Setujui</button>
                                    </form>
                                    <button type="button" data-url="{{ route('guru.peminjaman.tolak', $p) }}"
                                        data-nama="{{ $p->siswa->nama ?? '-' }}" onclick="openTolakModal(this)"
                                        class="{{ $btnOutlineDanger }} flex-1 px-3 py-2 text-xs lg:flex-none">Tolak</button>
                                </div>
                            @elseif($p->status === 'disetujui')
                                @if($p->tanggal_ambil->gt(today()))
                                    <p class="flex items-center gap-1.5 text-sm text-slate-500 lg:justify-end lg:text-right">
                                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Menunggu tanggal ambil
                                    </p>
                                @else
                                    <form method="POST" action="{{ route('guru.peminjaman.kembali', $p) }}"
                                        onsubmit="return confirm('Tandai dokumen sudah dikembalikan?')" class="lg:flex lg:justify-end" data-once>
                                        @csrf @method('PATCH')
                                        <button type="submit" class="{{ $btnSuccess }} w-full px-3 py-2 text-xs lg:w-auto">Tandai dikembalikan</button>
                                    </form>
                                @endif
                            @else
                                <p class="flex items-center gap-1.5 text-sm text-slate-400 lg:justify-end">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Selesai
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center px-6 py-14 text-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        @if($statusAktif !== '')
                            <p class="mt-4 text-sm font-semibold text-slate-700">Tidak ada peminjaman dengan status ini</p>
                            <p class="mt-1 max-w-xs text-sm text-slate-400">Pilih status lain untuk melihat pengajuan.</p>
                            <a href="{{ route('guru.peminjaman.index') }}" class="{{ $btnOutline }} mt-4 px-3 py-2 text-xs">Lihat semua</a>
                        @else
                            <p class="mt-4 text-sm font-semibold text-slate-700">Belum ada pengajuan peminjaman</p>
                            <p class="mt-1 max-w-xs text-sm text-slate-400">Pengajuan peminjaman dokumen dari siswa akan muncul di sini.</p>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        @if(method_exists($peminjamans, 'hasPages') && $peminjamans->hasPages())
            <div class="border-t border-slate-100 px-4 py-4 sm:px-6">{{ $peminjamans->withQueryString()->links() }}</div>
        @endif
    </section>

    {{-- MODAL TOLAK --}}
    <div id="tolakModal" role="dialog" aria-modal="true" aria-labelledby="tolakModalTitle"
        class="fixed inset-0 z-50 flex hidden items-end justify-center bg-slate-900/50 backdrop-blur-sm sm:items-center sm:p-4">
        <div class="modal-panel flex max-h-[92vh] w-full max-w-md flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl sm:rounded-2xl">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
                <h3 id="tolakModalTitle" class="text-base font-bold text-slate-900 sm:text-lg">Tolak peminjaman</h3>
                <button type="button" onclick="closeTolakModal()" aria-label="Tutup"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="overflow-y-auto px-5 py-5 sm:px-6">
                <form id="tolakForm" method="POST" class="space-y-4" data-once>
                    @csrf
                    @method('PATCH')
                    <p class="text-sm text-slate-500">Pengajuan dari <span id="tolakNama" class="font-semibold text-slate-800"></span></p>
                    <div>
                        <label for="catatanGuru" class="{{ $lbl }}">Alasan penolakan</label>
                        <textarea id="catatanGuru" name="catatan_guru" rows="3" required maxlength="300"
                            placeholder="Jelaskan alasan agar siswa tahu langkah selanjutnya" class="{{ $inp }}"></textarea>
                        <p class="mt-1 text-xs text-slate-400">Maksimal 300 karakter.</p>
                    </div>
                    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                        <button type="button" onclick="closeTolakModal()" class="{{ $btnOutline }} px-4 py-2.5 text-sm">Batal</button>
                        <button type="submit" class="{{ $btnDanger }} px-4 py-2.5 text-sm">Tolak pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const tolakModal = document.getElementById('tolakModal');

        function openTolakModal(btn) {
            document.getElementById('tolakForm').action = btn.dataset.url;
            document.getElementById('tolakNama').textContent = btn.dataset.nama;
            document.getElementById('catatanGuru').value = '';
            tolakModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            document.getElementById('catatanGuru').focus();
        }

        function closeTolakModal() {
            tolakModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Tutup saat klik area gelap atau tekan Esc
        tolakModal.addEventListener('click', (e) => { if (e.target === tolakModal) closeTolakModal(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeTolakModal(); });

        // Cegah kirim ganda (form bertanda data-once)
        document.addEventListener('submit', (e) => {
            const f = e.target;
            if (e.defaultPrevented || !f.matches('[data-once]')) return;
            if (f.dataset.busy === '1') { e.preventDefault(); return; }
            f.dataset.busy = '1';
            const b = f.querySelector('[type=submit]');
            b.dataset.label = b.textContent;
            b.disabled = true;
            b.textContent = 'Memproses...';
        });

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