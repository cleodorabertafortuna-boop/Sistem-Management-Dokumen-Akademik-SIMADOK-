<?php

namespace App\Http\Controllers;

use App\Models\PeminjamanDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanDokumenGuruController extends Controller
{
    // SESUAIKAN guard dengan yang dipakai di GuruController / SuratKeteranganController
    private function guru()
    {
        $guru = Auth::guard('guru')->user();
        abort_unless($guru && $guru->isKurikulum(), 403);
        return $guru;
    }

    public function index(Request $request)
    {
        $guru = $this->guru();

        $peminjamans = PeminjamanDokumen::with('siswa')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderByRaw("status = 'menunggu' desc")
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('guru.peminjaman.index', compact('guru', 'peminjamans'));
    }

    public function setujui(PeminjamanDokumen $peminjaman)
    {
        $guru = $this->guru();
        abort_unless($peminjaman->status === 'menunggu', 422);

        $peminjaman->update([
            'status' => 'disetujui',
            'guru_id' => $guru->id,
            'diproses_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan disetujui.');
    }

    public function tolak(Request $request, PeminjamanDokumen $peminjaman)
    {
        $guru = $this->guru();
        abort_unless($peminjaman->status === 'menunggu', 422);

        $data = $request->validate([
            'catatan_guru' => ['required', 'string', 'max:300'],
        ], ['catatan_guru.required' => 'Isi alasan penolakan.']);

        $peminjaman->update([
            'status' => 'ditolak',
            'catatan_guru' => $data['catatan_guru'],
            'guru_id' => $guru->id,
            'diproses_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan ditolak.');
    }

    public function kembali(PeminjamanDokumen $peminjaman)
    {
        $this->guru();
        abort_unless($peminjaman->status === 'disetujui', 422);

        if ($peminjaman->tanggal_ambil->gt(today())) {
            return back()->with('error', 'Dokumen belum bisa ditandai dikembalikan sebelum tanggal ambil ('
                . $peminjaman->tanggal_ambil->format('d M Y') . ').');
        }

        $peminjaman->update([
            'status' => 'dikembalikan',
            'dikembalikan_at' => now(),
        ]);

        return back()->with('success', 'Dokumen ditandai sudah dikembalikan.');
    }
}