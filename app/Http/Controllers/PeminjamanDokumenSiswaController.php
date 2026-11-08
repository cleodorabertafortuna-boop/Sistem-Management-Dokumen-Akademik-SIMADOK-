<?php

namespace App\Http\Controllers;

use App\Models\PeminjamanDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanDokumenSiswaController extends Controller
{
    // SESUAIKAN guard dengan yang dipakai di SiswaController / SuratKeteranganController
    private function siswa()
    {
        return Auth::guard('siswa')->user();
    }

    public function index()
    {
        $siswa = $this->siswa();

        $peminjamans = PeminjamanDokumen::where('siswa_id', $siswa->id)
            ->latest()
            ->get();

        // Satu pengajuan aktif dalam satu waktu
        $adaAktif = $peminjamans->contains(fn($p) => in_array($p->status, ['menunggu', 'disetujui']));

        return view('siswa.peminjaman.index', compact('siswa', 'peminjamans', 'adaAktif'));
    }

    public function store(Request $request)
    {
        $siswa = $this->siswa();

        $aktif = PeminjamanDokumen::where('siswa_id', $siswa->id)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->exists();

        if ($aktif) {
            return back()->with('error', 'Kamu masih punya pengajuan yang belum selesai.');
        }

        $data = $request->validate([
            'tanggal_ambil' => ['required', 'date', 'after_or_equal:today'],
            'dokumen' => ['required', 'string', 'min:3', 'max:150'],
            'alasan' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'tanggal_ambil.required' => 'Tanggal ambil wajib diisi.',
            'tanggal_ambil.after_or_equal' => 'Tanggal ambil tidak boleh sebelum hari ini.',
            'dokumen.required' => 'Dokumen yang dipinjam wajib diisi.',
            'dokumen.min' => 'Nama dokumen minimal 3 karakter.',
            'alasan.required' => 'Alasan wajib diisi.',
            'alasan.min' => 'Alasan minimal 10 karakter.',
        ]);

        PeminjamanDokumen::create($data + ['siswa_id' => $siswa->id]);

        return redirect()->route('siswa.peminjaman.index')
            ->with('success', 'Pengajuan terkirim. Tunggu konfirmasi guru kurikulum.');
    }
}