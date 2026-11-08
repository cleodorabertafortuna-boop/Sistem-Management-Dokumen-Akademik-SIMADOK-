<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Siswa;
use App\Support\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // Dashboard utama admin: ringkasan + tabel guru & siswa
    public function dashboard()
    {
        $totalGuru = Guru::count();
        $totalSiswa = Siswa::count();
        $totalKelas = count(Kelas::semua());
        $gurus = Guru::withCount('siswas')->latest()->take(5)->get();
        $siswas = Siswa::with('guru')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalGuru', 'totalSiswa', 'totalKelas', 'gurus', 'siswas'));
    }

    // ---------------- CRUD GURU ----------------
    public function guruIndex(Request $request)
    {
        $gurus = Guru::withCount('siswas')
            ->when($request->q, fn($q) => $q->where(function ($w) use ($request) {
                $w->where('nama', 'like', "%{$request->q}%")
                    ->orWhere('nip', 'like', "%{$request->q}%");
            }))
            ->orderBy('nama')
            ->paginate(10);

        return view('admin.guru.index', compact('gurus'));
    }

    public function guruCreate()
    {
        return view('admin.guru.create');
    }

    public function guruStore(Request $request)
    {
        // Guru kurikulum tidak terikat satu kelas
        if ($request->role === 'kurikulum') {
            $request->merge(['kelas_dibina' => null]);
        }

        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'nip' => 'required|string|max:30|unique:gurus,nip',
            'role' => 'required|in:wali_kelas,kurikulum',
            'kelas_dibina' => [
                'required_if:role,wali_kelas',
                'nullable',
                'string',
                'max:50',
                Rule::in(Kelas::semua()),
                Rule::unique('gurus', 'kelas_dibina'),
            ],
            'password' => 'required|string|min:6',
        ], [
            'kelas_dibina.in' => 'Kelas tidak valid. Pilih kelas dari daftar.',
            'kelas_dibina.unique' => 'Kelas ini sudah memiliki wali kelas.',
        ]);

        $data['password'] = Hash::make($data['password']);
        Guru::create($data);

        return redirect()->route('admin.guru.index')->with('status', 'Data guru berhasil ditambahkan.');
    }

    public function guruEdit(Guru $guru)
    {
        return view('admin.guru.edit', compact('guru'));
    }

    public function guruUpdate(Request $request, Guru $guru)
    {
        if ($request->role === 'kurikulum') {
            $request->merge(['kelas_dibina' => null]);
        }

        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'nip' => 'required|string|max:30|unique:gurus,nip,' . $guru->id,
            'role' => 'required|in:wali_kelas,kurikulum',
            'kelas_dibina' => [
                'required_if:role,wali_kelas',
                'nullable',
                'string',
                'max:50',
                Rule::in(Kelas::semua()),
                Rule::unique('gurus', 'kelas_dibina')->ignore($guru->id),
            ],
        ], [
            'kelas_dibina.in' => 'Kelas tidak valid. Pilih kelas dari daftar.',
            'kelas_dibina.unique' => 'Kelas ini sudah memiliki wali kelas.',
        ]);

        $guru->update($data);

        return redirect()->route('admin.guru.index')->with('status', 'Data guru berhasil diperbarui.');
    }

    public function guruDestroy(Guru $guru)
    {
        $guru->delete();
        return back()->with('status', 'Data guru berhasil dihapus.');
    }

    // ---------------- CRUD SISWA ----------------
    public function siswaIndex(Request $request)
    {
        $siswas = Siswa::with('guru')
            ->when($request->q, fn($q) => $q->where(function ($w) use ($request) {
                $w->where('nama', 'like', "%{$request->q}%")
                    ->orWhere('nis', 'like', "%{$request->q}%");
            }))
            ->when($request->kelas, fn($q) => $q->where('kelas', $request->kelas))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString(); // pencarian & filter kelas tetap aktif saat pindah halaman

        $kelasList = Guru::where('role', 'wali_kelas')->pluck('kelas_dibina')->unique();

        return view('admin.siswa.index', compact('siswas', 'kelasList'));
    }

    public function siswaCreate()
    {
        // Hanya guru wali kelas yang bisa dipilih sebagai wali kelas siswa
        $gurus = Guru::where('role', 'wali_kelas')->orderBy('kelas_dibina')->get();
        return view('admin.siswa.create', compact('gurus'));
    }

    public function siswaStore(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'nis' => 'required|string|max:30|unique:siswas,nis',
            'kelas' => ['required', 'string', 'max:50', Rule::in(Kelas::semua())],
            'guru_id' => 'nullable|exists:gurus,id',
            'password' => 'required|string|min:6',
        ], [
            'kelas.in' => 'Kelas tidak valid. Pilih kelas dari daftar.',
        ]);
        $data['password'] = Hash::make($data['password']);
        Siswa::create($data);

        return redirect()->route('admin.siswa.index')->with('status', 'Data siswa berhasil ditambahkan.');
    }

    public function siswaEdit(Siswa $siswa)
    {
        $gurus = Guru::where('role', 'wali_kelas')->orderBy('kelas_dibina')->get();
        return view('admin.siswa.edit', compact('siswa', 'gurus'));
    }

    public function siswaUpdate(Request $request, Siswa $siswa)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'nis' => 'required|string|max:30|unique:siswas,nis,' . $siswa->id,
            'kelas' => ['required', 'string', 'max:50', Rule::in(Kelas::semua())],
            'guru_id' => 'nullable|exists:gurus,id',
        ], [
            'kelas.in' => 'Kelas tidak valid. Pilih kelas dari daftar.',
        ]);
        $siswa->update($data);

        return redirect()->route('admin.siswa.index')->with('status', 'Data siswa berhasil diperbarui.');
    }

    public function siswaDestroy(Siswa $siswa)
    {
        $siswa->delete();
        return back()->with('status', 'Data siswa berhasil dihapus.');
    }

    // Monitoring detail satu siswa (lihat semua dokumen, read-only)
    public function siswaShow(Siswa $siswa)
    {
        $siswa->load('dokumens.guru', 'guru');
        return view('admin.siswa.show', compact('siswa'));
    }
}