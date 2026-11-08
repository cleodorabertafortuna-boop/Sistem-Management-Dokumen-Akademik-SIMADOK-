@extends('layouts.app')
@section('title', 'Surat Keterangan')
@section('page-title', 'Pengajuan Surat Keterangan')
@section('user-initial', $siswa->nama)

@section('sidebar-menu')
    @include('siswa._sidebar')
@endsection

@section('content')
    @php
        // ---- Gaya bersama (sama dengan Dashboard Siswa) ----
        $card = 'rounded-2xl border border-slate-200 bg-white shadow-sm';
        $cardHead = 'flex items-center gap-3 border-b border-slate-100 px-5 py-4';
        $chip = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700';
        $row = 'flex items-center justify-between gap-3 px-5 py-3';
        $inp = 'w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none';
        $lbl = 'block text-sm font-medium text-slate-700 mb-1.5';
        $err = 'mt-1.5 text-xs text-red-600';
        $sectionTitle = 'mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400';
        $badge = 'inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset';
        $btn = 'inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-70';
        $btnSm = 'inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-blue-600 px-2.5 py-1.5 text-xs font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500';

        // ---- Data ----
        $total = $riwayat->count();
        $selesai = $riwayat->where('status', 'selesai')->count();
        $ditolak = $riwayat->where('status', 'ditolak')->count();
        $diproses = $total - $selesai - $ditolak;
        $pct = fn($n) => $total > 0 ? (int) round($n / $total * 100) : 0;

        // Status lain (diajukan/diproses) dianggap "sedang diproses"
        $grup = fn($s) => match ($s) {
            'selesai' => 'selesai',
            'ditolak' => 'ditolak',
            default => 'berjalan',
        };
        $warnaStatus = fn($s) => match ($s) {
            'selesai' => 'bg-blue-50 text-blue-700 ring-blue-200',
            'ditolak' => 'bg-white text-slate-600 ring-slate-300',
            default => 'bg-slate-50 text-slate-500 ring-slate-200',
        };

        $stats = [
            ['label' => 'Total pengajuan', 'value' => $total, 'pct' => $total > 0 ? 100 : 0,
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ['label' => 'Diproses', 'value' => $diproses, 'pct' => $pct($diproses),
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Selesai', 'value' => $selesai, 'pct' => $pct($selesai),
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Ditolak', 'value' => $ditolak, 'pct' => $pct($ditolak),
                'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];

        $langkah = [
            ['judul' => 'Isi formulir', 'isi' => 'Lengkapi data diri, data orang tua, dan tujuan pengajuan surat.'],
            ['judul' => 'Diproses kurikulum', 'isi' => 'Data diperiksa, lalu nomor surat dibuat jika disetujui.'],
            ['judul' => 'Cetak surat', 'isi' => 'Setelah selesai, tekan tombol Cetak PDF pada riwayat pengajuanmu.'],
        ];

        $filters = [
            ['key' => 'semua', 'label' => 'Semua', 'count' => $total],
            ['key' => 'berjalan', 'label' => 'Diproses', 'count' => $diproses],
            ['key' => 'selesai', 'label' => 'Selesai', 'count' => $selesai],
            ['key' => 'ditolak', 'label' => 'Ditolak', 'count' => $ditolak],
        ];

        $iconCetak = 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z';

        // ---- Ikon ----
        $icoAlur = 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4';
        $icoForm = 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z';
        $icoRiwayat = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
    @endphp

    <div class="w-full space-y-4 sm:space-y-6">

        {{-- PESAN FLASH --}}
        @if(session('success') || session('error'))
            <div class="space-y-3" role="status">
                @if(session('success'))
                    <div data-flash class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        <p class="min-w-0 flex-1 break-words">{{ session('success') }}</p>
                        <button type="button" aria-label="Tutup pesan" onclick="this.closest('[data-flash]').remove()"
                            class="-m-1 rounded p-1 opacity-60 hover:opacity-100">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div data-flash class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <p class="min-w-0 flex-1 break-words">{{ session('error') }}</p>
                        <button type="button" aria-label="Tutup pesan" onclick="this.closest('[data-flash]').remove()"
                            class="-m-1 rounded p-1 opacity-60 hover:opacity-100">&times;</button>
                    </div>
                @endif
            </div>
        @endif

        {{-- ALUR --}}
        <section class="{{ $card }} overflow-hidden">
            <div class="{{ $cardHead }}">
                <div class="{{ $chip }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icoAlur }}" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h3 class="text-base font-bold text-slate-900">Alur pengajuan</h3>
                    <p class="text-xs text-slate-500">Tiga langkah mengajukan surat keterangan</p>
                </div>
            </div>
            <div class="p-5 sm:p-6">
                <ol class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6">
                    @foreach($langkah as $i => $l)
                        <li class="flex min-w-0 gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white">{{ $i + 1 }}</span>
                            <div class="min-w-0 pt-0.5">
                                <p class="text-sm font-semibold text-slate-800">{{ $l['judul'] }}</p>
                                <p class="mt-0.5 text-xs leading-relaxed text-slate-500">{{ $l['isi'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
                <p class="mt-5 rounded-lg bg-slate-50 px-3 py-2.5 text-xs leading-relaxed text-slate-600">
                    Pastikan NISN dan nama orang tua sama dengan dokumen resmi agar pengajuan tidak ditolak.
                </p>
            </div>
        </section>

        {{-- STATISTIK --}}
        <section aria-label="Ringkasan" class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
            @foreach($stats as $st)
                <div class="{{ $card }} px-4 py-4 sm:px-5">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm text-slate-500">{{ $st['label'] }}</p>
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $st['icon'] }}" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $st['value'] }}</p>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-blue-600" style="width: {{ $st['pct'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- FORM + RIWAYAT --}}
        <div class="space-y-4 sm:space-y-6">

            {{-- FORM PENGAJUAN --}}
            <section class="{{ $card }} min-w-0 overflow-hidden">
                <div class="{{ $cardHead }}">
                    <div class="{{ $chip }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icoForm }}" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-slate-900">Ajukan Surat Keterangan</h3>
                        <p class="break-words text-xs text-slate-500">Atas nama <span class="font-medium text-slate-700">{{ $siswa->nama }}</span> &middot; NIS {{ $siswa->nis }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('siswa.surat.store') }}" class="p-5 sm:p-6" id="form-surat">
                    @csrf

                    {{-- Data diri --}}
                    <p class="{{ $sectionTitle }}">Data diri</p>
                    <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="min-w-0">
                            <label for="tempat_lahir" class="{{ $lbl }}">Tempat Lahir</label>
                            <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required placeholder="Masukkan tempat lahir" class="{{ $inp }}">
                            @error('tempat_lahir')
                                <p class="{{ $err }}">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="min-w-0">
                            <label for="tanggal_lahir" class="{{ $lbl }}">Tanggal Lahir</label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required class="{{ $inp }}">
                            @error('tanggal_lahir')
                                <p class="{{ $err }}">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="min-w-0">
                            <label for="jenis_kelamin" class="{{ $lbl }}">Jenis Kelamin</label>
                            <select id="jenis_kelamin" name="jenis_kelamin" required class="{{ $inp }}">
                                <option value="">Pilih jenis kelamin</option>
                                <option value="Laki-Laki" @selected(old('jenis_kelamin') == 'Laki-Laki')>Laki-Laki</option>
                                <option value="Perempuan" @selected(old('jenis_kelamin') == 'Perempuan')>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <p class="{{ $err }}">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="min-w-0">
                            <label for="nisn" class="{{ $lbl }}">NISN</label>
                            <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" required inputmode="numeric" placeholder="Masukkan NISN" class="{{ $inp }}">
                            @error('nisn')
                                <p class="{{ $err }}">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Data orang tua --}}
                    <div class="mt-6 border-t border-slate-100 pt-6">
                        <p class="{{ $sectionTitle }}">Data orang tua &amp; alamat</p>
                        <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div class="min-w-0">
                                <label for="nama_ayah" class="{{ $lbl }}">Nama Ayah</label>
                                <input type="text" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah') }}" required placeholder="Masukkan nama ayah" class="{{ $inp }}">
                                @error('nama_ayah')
                                    <p class="{{ $err }}">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="min-w-0">
                                <label for="nama_ibu" class="{{ $lbl }}">Nama Ibu</label>
                                <input type="text" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu') }}" required placeholder="Masukkan nama ibu" class="{{ $inp }}">
                                @error('nama_ibu')
                                    <p class="{{ $err }}">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="min-w-0 sm:col-span-2">
                                <label for="alamat" class="{{ $lbl }}">Alamat Lengkap</label>
                                <textarea id="alamat" name="alamat" rows="3" required placeholder="Tuliskan alamat lengkap"
                                    class="{{ $inp }} min-h-[5.5rem] resize-y">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <p class="{{ $err }}">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Pengajuan --}}
                    <div class="mt-6 border-t border-slate-100 pt-6">
                        <p class="{{ $sectionTitle }}">Pengajuan</p>
                        <div class="min-w-0">
                            <label for="tujuan_pengajuan" class="{{ $lbl }}">Tujuan Pengajuan</label>
                            <input type="text" id="tujuan_pengajuan" name="tujuan_pengajuan"
                                value="{{ old('tujuan_pengajuan') }}" required placeholder="Contoh: Beasiswa"
                                class="{{ $inp }}">
                            @error('tujuan_pengajuan')
                                <p class="{{ $err }}">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-slate-500">Surat diproses oleh kurikulum, lalu bisa kamu cetak sendiri.</p>
                        <button type="submit" id="btn-kirim" class="{{ $btn }}">
                            <svg id="btn-kirim-spin" class="hidden h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span id="btn-kirim-text">Ajukan Surat</span>
                        </button>
                    </div>
                </form>
            </section>

            {{-- RIWAYAT PENGAJUAN --}}
            <section class="{{ $card }} min-w-0 overflow-hidden">
                <div class="{{ $cardHead }}">
                    <div class="{{ $chip }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icoRiwayat }}" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-slate-900">Riwayat Pengajuan</h3>
                        <p class="text-xs text-slate-500">{{ $total ? $total . ' pengajuan' : 'Belum ada pengajuan' }}</p>
                    </div>
                </div>

                @if($riwayat->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada pengajuan surat</p>
                        <p class="mt-1 text-sm text-slate-400">Isi formulir pengajuan. Setelah dikirim, statusnya akan muncul di sini.</p>
                    </div>
                @else
                    {{-- Filter --}}
                    <div class="flex gap-2 overflow-x-auto border-b border-slate-100 px-5 py-3" role="tablist" aria-label="Filter riwayat" id="filter-bar">
                        @foreach($filters as $f)
                            <button type="button" role="tab" data-filter="{{ $f['key'] }}"
                                class="filter-chip inline-flex shrink-0 items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-semibold focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                {{ $f['label'] }}
                                <span class="rounded-full bg-black/5 px-1.5 py-0.5 text-[10px] leading-none">{{ $f['count'] }}</span>
                            </button>
                        @endforeach
                    </div>

                    <ul class="divide-y divide-slate-100" id="riwayat-list">
                        @foreach($riwayat as $r)
                            @php
                                $ket = $r->status === 'ditolak' && $r->catatan
                                    ? 'Catatan: ' . $r->catatan
                                    : 'Diajukan ' . $r->created_at->format('d M Y') . ($r->status === 'selesai' ? ' · No. ' . $r->nomor_surat : '');
                            @endphp
                            <li data-status="{{ $grup($r->status) }}" class="{{ $row }}">
                                <p class="min-w-0 flex-1 truncate text-sm" title="{{ $r->tujuan_pengajuan }} — {{ $ket }}">
                                    <span class="font-medium text-slate-700">{{ $r->tujuan_pengajuan }}</span>
                                    <span class="text-slate-400"> &middot; {{ $ket }}</span>
                                </p>
                                <span class="{{ $badge }} {{ $warnaStatus($r->status) }} shrink-0">{{ $r->label_status }}</span>
                                @if($r->status === 'selesai')
                                    <a href="{{ route('siswa.surat.cetak', $r) }}" target="_blank" rel="noopener" title="Cetak PDF" aria-label="Cetak PDF"
                                        class="{{ $btnSm }}">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconCetak }}" />
                                        </svg>
                                        <span class="hidden sm:inline">Cetak PDF</span>
                                    </a>
                                @elseif($r->status !== 'ditolak')
                                    <span aria-disabled="true" title="Surat bisa dicetak setelah dikonfirmasi kurikulum"
                                        class="inline-flex shrink-0 cursor-not-allowed select-none items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-400">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4" />
                                        </svg>
                                        <span class="hidden sm:inline">Cetak PDF</span>
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    <p id="filter-empty" class="hidden px-5 py-8 text-center text-sm text-slate-500">Tidak ada pengajuan pada kategori ini.</p>
                @endif
            </section>
        </div>
    </div>

    <script>
        (function () {
            // Filter riwayat
            var bar = document.getElementById('filter-bar');
            if (bar) {
                var items = document.querySelectorAll('#riwayat-list > li');
                var empty = document.getElementById('filter-empty');
                var on = ['bg-blue-600', 'border-blue-600', 'text-white'];
                var off = ['bg-white', 'border-slate-200', 'text-slate-600', 'hover:bg-slate-50'];

                function apply(key) {
                    var shown = 0;
                    items.forEach(function (li) {
                        var ok = key === 'semua' || li.dataset.status === key;
                        li.classList.toggle('hidden', !ok);
                        if (ok) shown++;
                    });
                    empty.classList.toggle('hidden', shown > 0);
                    bar.querySelectorAll('.filter-chip').forEach(function (b) {
                        var active = b.dataset.filter === key;
                        b.setAttribute('aria-selected', active);
                        on.forEach(function (c) { b.classList.toggle(c, active); });
                        off.forEach(function (c) { b.classList.toggle(c, !active); });
                    });
                }
                bar.addEventListener('click', function (e) {
                    var b = e.target.closest('.filter-chip');
                    if (b) apply(b.dataset.filter);
                });
                apply('semua');
            }

            // Pesan sukses hilang sendiri setelah 6 detik
            document.querySelectorAll('[data-flash].bg-emerald-50').forEach(function (el) {
                setTimeout(function () { el.remove(); }, 6000);
            });

            // Cegah kirim ganda
            var form = document.getElementById('form-surat');
            if (form) {
                form.addEventListener('submit', function () {
                    var btn = document.getElementById('btn-kirim');
                    btn.disabled = true;
                    document.getElementById('btn-kirim-spin').classList.remove('hidden');
                    document.getElementById('btn-kirim-text').textContent = 'Mengirim...';
                });
            }
        })();
    </script>
@endsection