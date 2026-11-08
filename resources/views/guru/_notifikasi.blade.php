@php
    $peminjamanBaru = $peminjamanBaru ?? collect();
    $suratBaru = $suratBaru ?? collect();

    $urlSurat = route('guru.surat.index', ['status' => 'diajukan']);
    $urlPeminjaman = route('guru.peminjaman.index', ['status' => 'menunggu']);

    // Gabungkan kedua jenis pengajuan, yang terbaru di atas
    $daftar = collect();

    foreach ($suratBaru as $s) {
        $daftar->push([
            'jenis' => 'Surat Keterangan',
            'warna' => 'bg-blue-50 text-blue-700 border-blue-100',
            'siswa' => $s->siswa->nama ?? '-',
            'ket' => $s->tujuan_pengajuan,
            'waktu' => $s->created_at,
            'url' => $urlSurat,
        ]);
    }

    foreach ($peminjamanBaru as $p) {
        $daftar->push([
            'jenis' => 'Peminjaman Dokumen',
            'warna' => 'bg-purple-50 text-purple-700 border-purple-100',
            'siswa' => $p->siswa->nama ?? '-',
            'ket' => $p->dokumen . ' (ambil ' . $p->tanggal_ambil->translatedFormat('d M Y') . ')',
            'waktu' => $p->created_at,
            'url' => $urlPeminjaman,
        ]);
    }

    $daftar = $daftar->sortByDesc('waktu')->values();
    $total = $daftar->count();
    $tampil = $daftar->take(8);
@endphp

@if($guru->isKurikulum() && $total > 0)
    <div class="bg-white rounded-2xl border border-amber-200 shadow-sm overflow-hidden mb-4">
        {{-- Judul --}}
        <div class="px-5 py-4 bg-amber-50 border-b border-amber-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <div class="min-w-0">
                <h2 class="font-semibold text-amber-900">Pengajuan Menunggu Konfirmasi</h2>
                <p class="text-sm text-amber-800/80">{{ $total }} pengajuan dari siswa perlu Anda tindak lanjuti.</p>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr class="text-left">
                        <th class="px-5 py-3 font-semibold">Siswa</th>
                        <th class="px-5 py-3 font-semibold">Jenis</th>
                        <th class="px-5 py-3 font-semibold hidden md:table-cell">Keterangan</th>
                        <th class="px-5 py-3 font-semibold hidden sm:table-cell whitespace-nowrap">Diajukan</th>
                        <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tampil as $n)
                        <tr class="table-row-hover">
                            <td class="px-5 py-3 font-medium text-slate-700">
                                {{ $n['siswa'] }}
                                {{-- Keterangan ditampilkan di bawah nama pada layar kecil --}}
                                <span
                                    class="block md:hidden text-xs font-normal text-slate-400 break-words">{{ $n['ket'] }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="inline-block whitespace-nowrap px-2.5 py-1 rounded-full border text-xs font-medium {{ $n['warna'] }}">
                                    {{ $n['jenis'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-500 hidden md:table-cell">
                                <span class="block max-w-xs truncate" title="{{ $n['ket'] }}">{{ $n['ket'] }}</span>
                            </td>
                            <td class="px-5 py-3 text-slate-500 hidden sm:table-cell whitespace-nowrap">
                                {{ $n['waktu']->translatedFormat('d M Y') }}
                                <span class="block text-xs text-slate-400">{{ $n['waktu']->diffForHumans() }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ $n['url'] }}"
                                    class="inline-block bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($total > $tampil->count())
            <div
                class="px-5 py-3 border-t border-slate-100 text-xs text-slate-500 flex flex-wrap items-center justify-between gap-2">
                <span>Menampilkan {{ $tampil->count() }} dari {{ $total }} pengajuan.</span>
                <span class="flex gap-3 font-semibold">
                    @if($suratBaru->isNotEmpty())
                        <a href="{{ $urlSurat }}" class="text-blue-700 hover:text-blue-900">Semua surat</a>
                    @endif
                    @if($peminjamanBaru->isNotEmpty())
                        <a href="{{ $urlPeminjaman }}" class="text-blue-700 hover:text-blue-900">Semua peminjaman</a>
                    @endif
                </span>
            </div>
        @endif
    </div>
@endif