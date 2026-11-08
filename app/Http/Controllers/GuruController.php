<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\PeminjamanDokumen;
use App\Models\Siswa;
use App\Models\SuratKeterangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    // Dashboard guru: wali kelas lihat siswa binaan, kurikulum lihat semua siswa (dengan search & filter kelas)
    public function dashboard(Request $request)
    {
        $guru = Auth::guard('guru')->user();

        $peminjamanBaru = collect();
        $suratBaru = collect();

        if ($guru->isKurikulum()) {
            $siswas = Siswa::withCount('dokumens')
                ->when($request->q, fn($q) => $q->where(function ($w) use ($request) {
                    $w->where('nama', 'like', "%{$request->q}%")
                        ->orWhere('nis', 'like', "%{$request->q}%");
                }))
                ->when($request->kelas, fn($q) => $q->where('kelas', $request->kelas))
                ->orderBy('nama')
                ->paginate(10)
                ->withQueryString();

            $kelasList = Siswa::distinct()->orderBy('kelas')->pluck('kelas');

            // Pengajuan yang menunggu konfirmasi kurikulum
            $peminjamanBaru = PeminjamanDokumen::with('siswa:id,nama')
                ->where('status', 'menunggu')
                ->latest()
                ->get();

            $suratBaru = SuratKeterangan::with('siswa:id,nama')
                ->where('status', 'diajukan')
                ->latest()
                ->get();
        } else {
            // Wali kelas: hanya siswa binaannya, bisa dicari berdasarkan nama atau NIS
            $siswas = $guru->siswas()->withCount('dokumens')
                ->when($request->q, fn($q) => $q->where(function ($w) use ($request) {
                    $w->where('nama', 'like', "%{$request->q}%")
                        ->orWhere('nis', 'like', "%{$request->q}%");
                }))
                ->orderBy('nama')
                ->paginate(10)
                ->withQueryString();
            $kelasList = collect();
        }

        return view('guru.dashboard', compact('guru', 'siswas', 'kelasList', 'peminjamanBaru', 'suratBaru'));
    }

    // Halaman kelola dokumen milik satu siswa
    public function dokumenSiswa($siswaId)
    {
        $guru = Auth::guard('guru')->user();

        if ($guru->isKurikulum()) {
            $siswa = Siswa::with('dokumens')->findOrFail($siswaId);
        } else {
            $siswa = $guru->siswas()->with('dokumens')->findOrFail($siswaId);
        }

        return view('guru.dokumen', compact('siswa', 'guru'));
    }

    public function dokumenStore(Request $request, $siswaId)
    {
        $guru = Auth::guard('guru')->user();

        if ($guru->isWaliKelas()) {
            // Wali kelas hanya boleh mengunggah raport, untuk siswa binaannya sendiri
            $siswa = $guru->siswas()->findOrFail($siswaId);

            $data = $request->validate([
                'semester' => 'required|integer|min:1|max:6',
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            $kategori = 'raport';
            $jenisSertifikat = null;
            $semester = $data['semester'];
        } else {
            // Kurikulum hanya boleh mengunggah ijazah & sertifikat, untuk siswa manapun
            $siswa = Siswa::findOrFail($siswaId);

            $data = $request->validate([
                'kategori' => 'required|in:ijazah,sertifikat',
                'jenis_sertifikat' => 'required_if:kategori,sertifikat|nullable|in:TOEIC,TKA,UKK,PKL',
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            $kategori = $data['kategori'];
            $jenisSertifikat = $data['jenis_sertifikat'] ?? null;
            $semester = null;
        }

        $path = $request->file('file')->store('dokumen/' . $siswa->nis, 'public');
        $namaFile = $request->file('file')->getClientOriginalName();

        $digantikan = 0;

        DB::transaction(function () use ($siswa, $guru, $kategori, $jenisSertifikat, $semester, $path, $namaFile, &$digantikan) {
            // Versi sebelumnya (jenis dokumen yang sama) dipindahkan ke riwayat lewat soft delete,
            // file fisiknya tetap tersimpan sebagai jejak.
            $sebelumnya = Dokumen::where('siswa_id', $siswa->id)->where('kategori', $kategori);

            if ($kategori === 'raport') {
                $sebelumnya->where('semester', $semester);
            } elseif ($kategori === 'sertifikat') {
                $sebelumnya->where('jenis_sertifikat', $jenisSertifikat);
            }

            $digantikan = $sebelumnya->delete();

            Dokumen::create([
                'siswa_id' => $siswa->id,
                'kategori' => $kategori,
                'kelas_saat_unggah' => $siswa->kelas,
                'diunggah_oleh_nama' => $guru->nama,
                'jenis_sertifikat' => $jenisSertifikat,
                'semester' => $semester,
                'nama_file' => $namaFile,
                'file_path' => $path,
                'uploaded_by' => $guru->id,
            ]);
        });

        $pesan = $digantikan > 0
            ? 'Dokumen berhasil diunggah. Versi sebelumnya tersimpan di riwayat unggahan.'
            : 'Dokumen berhasil diunggah.';

        return back()->with('status', $pesan);
    }

    // Mengganti file dokumen: versi lama masuk riwayat, versi baru dicatat atas nama guru yang mengganti
    public function dokumenUpdate(Request $request, Dokumen $dokumen)
    {
        $guru = Auth::guard('guru')->user();
        $this->authorizeDokumenAccess($guru, $dokumen);

        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $siswa = $dokumen->siswa;
        $path = $request->file('file')->store('dokumen/' . $siswa->nis, 'public');
        $namaFile = $request->file('file')->getClientOriginalName();

        DB::transaction(function () use ($dokumen, $siswa, $guru, $path, $namaFile) {
            $baru = $dokumen->replicate();
            $baru->kelas_saat_unggah = $siswa->kelas;
            $baru->diunggah_oleh_nama = $guru->nama;
            $baru->uploaded_by = $guru->id;
            $baru->uploaded_by_admin_id = null;
            $baru->nama_file = $namaFile;
            $baru->file_path = $path;
            $baru->save();

            // Versi lama tetap ada di riwayat, file fisiknya tidak dihapus
            $dokumen->delete();
        });

        return back()->with('status', 'Dokumen berhasil diperbarui. Versi sebelumnya tersimpan di riwayat unggahan.');
    }

    // Soft delete: baris dan file fisik dipertahankan sebagai jejak
    public function dokumenDestroy(Dokumen $dokumen)
    {
        $guru = Auth::guard('guru')->user();
        $this->authorizeDokumenAccess($guru, $dokumen);

        $dokumen->delete();

        return back()->with('status', 'Dokumen berhasil dihapus.');
    }

    // Memastikan guru hanya bisa mengubah/menghapus dokumen sesuai kewenangan rolenya
    private function authorizeDokumenAccess($guru, Dokumen $dokumen): void
    {
        if ($guru->isWaliKelas()) {
            abort_unless(
                $dokumen->kategori === 'raport' && $dokumen->siswa->guru_id === $guru->id,
                403,
                'Wali kelas hanya dapat mengelola raport siswa binaannya sendiri.'
            );
        } else {
            abort_unless(
                in_array($dokumen->kategori, ['ijazah', 'sertifikat']),
                403,
                'Kurikulum hanya dapat mengelola ijazah dan sertifikat.'
            );
        }
    }
}