{{-- Sidebar siswa (dipakai semua halaman siswa supaya tampilannya konsisten) --}}
@php
    $menus = [
        [
            'route' => 'siswa.dashboard',
            'label' => 'Dokumen Saya',
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ],
        [
            'route' => 'siswa.surat.index',
            'label' => 'Surat Keterangan',
            'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        ],
        [
            'route' => 'siswa.peminjaman.index',
            'label' => 'Pinjam Dokumen',
            'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        ],
    ];
@endphp

<div class="mx-1 mb-4 flex items-center gap-3 rounded-xl bg-white/5 px-3 py-3 ring-1 ring-white/10">
    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 text-sm font-bold text-white"
        aria-hidden="true">{{ strtoupper(mb_substr($siswa->nama, 0, 1)) }}</span>
    <div class="min-w-0">
        <p class="truncate text-sm font-semibold text-white">{{ $siswa->nama }}</p>
        <p class="truncate text-xs text-primary-200/80">Kelas {{ $siswa->kelas }}</p>
    </div>
</div>

<nav class="space-y-1" aria-label="Menu siswa">
    @foreach($menus as $m)
        @php $active = request()->routeIs($m['route']); @endphp
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

@include('siswa._notifikasi')