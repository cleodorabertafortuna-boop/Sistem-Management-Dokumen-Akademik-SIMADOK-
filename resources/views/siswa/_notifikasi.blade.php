{{-- Notifikasi di sidebar siswa: peminjaman & surat yang sudah diproses --}}
@php
    $siswaNotif = $siswa ?? Auth::guard('siswa')->user();

    // Data dari controller bila ada; kalau tidak (halaman lain), ambil 7 hari terakhir
    $notifPeminjaman = $notifPeminjaman ?? \App\Models\PeminjamanDokumen::where('siswa_id', $siswaNotif->id)
        ->whereIn('status', ['disetujui', 'ditolak'])
        ->where('updated_at', '>=', now()->subDays(7))
        ->latest('updated_at')->get();

    $notifSurat = $notifSurat ?? \App\Models\SuratKeterangan::where('siswa_id', $siswaNotif->id)
        ->whereIn('status', ['selesai', 'ditolak'])
        ->where('updated_at', '>=', now()->subDays(7))
        ->latest('updated_at')->get();

    $notifItems = collect();

    foreach ($notifPeminjaman as $p) {
        $ok = $p->status === 'disetujui';
        $notifItems->push([
            'ok' => $ok,
            'judul' => $ok ? 'Peminjaman disetujui' : 'Peminjaman ditolak',
            'isi' => $ok
                ? $p->dokumen . ' — ambil ' . $p->tanggal_ambil->translatedFormat('d M Y')
                : $p->dokumen . ' — ' . ($p->catatan_guru ?: 'tanpa catatan'),
            'url' => route('siswa.peminjaman.index'),
            'waktu' => $p->diproses_at ?? $p->updated_at,
        ]);
    }

    foreach ($notifSurat as $s) {
        $ok = $s->status === 'selesai';
        $notifItems->push([
            'ok' => $ok,
            'judul' => $ok ? 'Surat keterangan selesai' : 'Surat keterangan ditolak',
            'isi' => $ok
                ? 'No. ' . ($s->nomor_surat ?: '-') . ' — siap dicetak'
                : ($s->catatan ?: 'tanpa catatan'),
            'url' => route('siswa.surat.index'),
            'waktu' => $s->updated_at,
        ]);
    }

    $notifItems = $notifItems->sortByDesc('waktu')->values();
@endphp

@if($notifItems->isNotEmpty())
    <details open class="group mx-1 mt-4 rounded-xl bg-white/5 ring-1 ring-white/10">
        <summary
            class="flex cursor-pointer list-none items-center gap-2.5 px-3 py-2.5 text-sm font-medium text-white [&::-webkit-details-marker]:hidden">
            <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="flex-1">Notifikasi</span>
            <span
                class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-white px-1.5 text-[11px] font-bold leading-none text-primary-700">{{ $notifItems->count() }}</span>
            <svg class="h-4 w-4 shrink-0 text-white/60 group-open:rotate-180" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </summary>

        <ul class="max-h-72 divide-y divide-white/10 overflow-y-auto border-t border-white/10">
            @foreach($notifItems as $it)
                <li>
                    <a href="{{ $it['url'] }}"
                        class="block px-3 py-2.5 hover:bg-white/10 focus:outline-none focus-visible:bg-white/10">
                        <p class="flex items-center gap-1.5 text-xs font-semibold text-white">
                            <span
                                class="h-1.5 w-1.5 shrink-0 rounded-full {{ $it['ok'] ? 'bg-emerald-300' : 'bg-red-300' }}"></span>
                            <span class="truncate">{{ $it['judul'] }}</span>
                        </p>
                        <p class="mt-0.5 line-clamp-2 break-words text-xs text-white/70">{{ $it['isi'] }}</p>
                        @if($it['waktu'])
                            <p class="mt-0.5 text-[11px] text-white/50">{{ $it['waktu']->locale('id')->diffForHumans() }}</p>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </details>
@endif