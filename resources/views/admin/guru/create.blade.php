@extends('layouts.app')
@section('title', 'Tambah Guru')
@section('page-title', 'Tambah Data Guru')
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
            <h2 class="text-base font-semibold text-slate-800">Data guru baru</h2>
            <p class="text-sm text-slate-400">Lengkapi data di bawah untuk menambahkan akun guru.</p>
        </div>

        <form method="POST" action="{{ route('admin.guru.store') }}" class="p-4 sm:p-7 space-y-4 sm:space-y-5">
            @csrf

            {{-- Nama --}}
            <div>
                <label for="nama" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                    placeholder="Masukkan nama lengkap guru"
                    class="w-full rounded-lg border border-slate-300 px-3 sm:px-4 py-2.5 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 outline-none">
                @error('nama')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- NIP --}}
            <div>
                <label for="nip" class="block text-sm font-medium text-slate-700 mb-1">NIP</label>
                <input type="text" id="nip" name="nip" value="{{ old('nip') }}" required inputmode="numeric"
                    placeholder="Masukkan Nomor Induk Pegawai (NIP)"
                    class="w-full rounded-lg border border-slate-300 px-3 sm:px-4 py-2.5 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 outline-none">
                @error('nip')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Peran --}}
            <div>
                <label for="roleSelect" class="block text-sm font-medium text-slate-700 mb-1">Peran Guru</label>
                <select name="role" id="roleSelect" onchange="toggleKelasField()" required
                    class="w-full rounded-lg border border-slate-300 px-3 sm:px-4 py-2.5 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">-- Pilih Peran --</option>
                    <option value="wali_kelas" @selected(old('role') == 'wali_kelas')>Wali Kelas — kelola raport siswa binaan
                    </option>
                    <option value="kurikulum" @selected(old('role') == 'kurikulum')>Guru Kurikulum — kelola ijazah &amp;
                        sertifikat semua siswa</option>
                </select>
                @error('role')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kelas dibina (DIUBAH: daftar dari config/kelas.php) --}}
            <div id="kelasField">
                <label for="kelas_dibina" class="block text-sm font-medium text-slate-700 mb-1">Kelas yang Dibina</label>
                <select name="kelas_dibina" id="kelas_dibina"
                    class="w-full rounded-lg border border-slate-300 px-3 sm:px-4 py-2.5 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach(\App\Support\Kelas::grouped() as $angkatan => $daftarKelas)
                        <optgroup label="Kelas {{ $angkatan }}">
                            @foreach($daftarKelas as $kelasValue)
                                <option value="{{ $kelasValue }}" @selected(old('kelas_dibina') == $kelasValue)>
                                    {{ $kelasValue }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400 mt-1">Hanya diisi untuk Wali Kelas. Satu wali kelas hanya membina satu
                    kelas.</p>
                @error('kelas_dibina')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password Awal</label>
                <input type="password" id="password" name="password" required autocomplete="new-password"
                    placeholder="Masukkan password awal (minimal 6 karakter)"
                    class="w-full rounded-lg border border-slate-300 px-3 sm:px-4 py-2.5 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 outline-none">
                @error('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit"
                    class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white font-semibold px-6 py-2.5 rounded-lg transition shadow-md shadow-blue-100">
                    Simpan
                </button>
                <a href="{{ route('admin.guru.index') }}"
                    class="w-full sm:w-auto text-center px-5 py-2.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        function toggleKelasField() {
            const role = document.getElementById('roleSelect').value;
            document.getElementById('kelasField').classList.toggle('hidden', role !== 'wali_kelas');
        }
        toggleKelasField();
    </script>
@endsection