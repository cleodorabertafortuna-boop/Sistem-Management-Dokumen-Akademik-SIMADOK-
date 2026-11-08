@extends('layouts.app')
@section('title', 'Edit Siswa')
@section('page-title', 'Edit Data Siswa')
@section('user-initial', 'A')

@section('sidebar-menu')
    @php
        $adminLogin = Auth::guard('web')->user();

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

    <div class="px-4 py-3 mb-1">
        <p class="text-sm font-semibold text-white">{{ $adminLogin->nama ?? $adminLogin->name ?? 'Admin' }}</p>
        <p class="text-xs text-primary-200/80">Administrator</p>
    </div>

    @foreach($menus as $m)
        <a href="{{ route($m['route']) }}"
            class="flex items-center gap-2.5 px-4 py-2 mx-1 rounded-lg text-sm font-medium transition
                                        {{ request()->routeIs(...$m['match']) ? 'text-white bg-white/10' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $m['icon'] }}" />
            </svg>
            {{ $m['label'] }}
        </a>
    @endforeach
@endsection

@section('content')
    <div class="w-full bg-white rounded-xl sm:rounded-2xl border border-blue-100 shadow-sm overflow-hidden">

        {{-- Header card --}}
        <div class="px-4 sm:px-7 py-4 sm:py-5 border-b border-slate-100">
            <h2 class="text-base font-semibold text-slate-800">Ubah data siswa</h2>
            <p class="text-sm text-slate-400">Perbarui data di bawah untuk mengubah informasi akun siswa.</p>
        </div>

        <form method="POST" action="{{ route('admin.siswa.update', $siswa) }}" class="p-4 sm:p-7 space-y-4 sm:space-y-5">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div>
                <label for="nama" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $siswa->nama) }}" required
                    placeholder="Masukkan nama lengkap siswa"
                    class="w-full rounded-lg border border-slate-300 px-3 sm:px-4 py-2.5 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 outline-none">
                @error('nama')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- NIS --}}
            <div>
                <label for="nis" class="block text-sm font-medium text-slate-700 mb-1">NIS</label>
                <input type="text" id="nis" name="nis" value="{{ old('nis', $siswa->nis) }}" required inputmode="numeric"
                    placeholder="Masukkan Nomor Induk Siswa (NIS)"
                    class="w-full rounded-lg border border-slate-300 px-3 sm:px-4 py-2.5 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 outline-none">
                @error('nis')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kelas --}}
            <div>
                <label for="kelas" class="block text-sm font-medium text-slate-700 mb-1">Kelas</label>
                <select name="kelas" id="kelas" required
                    class="w-full rounded-lg border border-slate-300 px-3 sm:px-4 py-2.5 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach(\App\Support\Kelas::grouped() as $angkatan => $daftarKelas)
                        <optgroup label="Kelas {{ $angkatan }}">
                            @foreach($daftarKelas as $kelasValue)
                                <option value="{{ $kelasValue }}" @selected(old('kelas', $siswa->kelas) == $kelasValue)>
                                    {{ $kelasValue }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('kelas')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Wali kelas --}}
            <div>
                <label for="guru_id" class="block text-sm font-medium text-slate-700 mb-1">Wali Kelas (Guru Pembina)</label>
                <select name="guru_id" id="guru_id"
                    class="w-full rounded-lg border border-slate-300 px-3 sm:px-4 py-2.5 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($gurus as $g)
                        <option value="{{ $g->id }}" @selected(old('guru_id', $siswa->guru_id) == $g->id)>{{ $g->nama }}
                            ({{ $g->kelas_dibina }})</option>
                    @endforeach
                </select>
                @error('guru_id')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Info password --}}
            <div class="rounded-lg bg-blue-50 border border-blue-100 px-4 py-3 text-xs text-blue-700">
                Password siswa tidak diubah lewat form ini.
            </div>

            {{-- Tombol --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit"
                    class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white font-semibold px-6 py-2.5 rounded-lg transition shadow-md shadow-blue-100">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.siswa.index') }}"
                    class="w-full sm:w-auto text-center px-5 py-2.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection