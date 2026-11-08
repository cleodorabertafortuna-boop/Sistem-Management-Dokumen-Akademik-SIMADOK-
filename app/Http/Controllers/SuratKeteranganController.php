<?php

namespace App\Http\Controllers;

use App\Models\SuratKeterangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratKeteranganController extends Controller
{
    // ================= SISWA =================

    // Halaman form pengajuan + riwayat pengajuan milik siswa yang login
    public function siswaIndex()
    {
        $siswa = Auth::guard('siswa')->user();
        $riwayat = $siswa->suratKeterangans()->latest()->get();

        return view('siswa.surat.index', compact('siswa', 'riwayat'));
    }

    public function siswaStore(Request $request)
    {   
        $siswa = Auth::guard('siswa')->user();

        $data = $request->validate([
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'nisn' => 'required|string|max:20',
            'nama_ayah' => 'required|string|max:150',
            'nama_ibu' => 'required|string|max:150',
            'alamat' => 'required|string',
            'tujuan_pengajuan' => 'required|string|max:255',
        ]);

        $data['siswa_id'] = $siswa->id;
        $data['status'] = 'diajukan';

        SuratKeterangan::create($data);

        return back()->with('status', 'Pengajuan surat keterangan berhasil dikirim. Silakan tunggu diproses oleh guru kurikulum.');
    }

    // Siswa mencetak suratnya sendiri, hanya jika sudah dikonfirmasi guru (status selesai)
    public function siswaCetak(SuratKeterangan $surat)
    {
        $siswa = Auth::guard('siswa')->user();

        // Surat milik siswa lain dibuat seolah-olah tidak ada
        abort_unless($surat->siswa_id === $siswa->id, 404);
        abort_unless($surat->status === 'selesai', 404, 'Surat belum diproses.');

        return $this->buatPdf($surat);
    }

    // ================= GURU KURIKULUM =================

    // Daftar seluruh pengajuan surat dari semua siswa
    public function guruIndex(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        abort_unless($guru->isKurikulum(), 403);

        $surats = SuratKeterangan::with('siswa')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10);

        return view('guru.surat.index', compact('surats'));
    }

    // Proses: isi nomor surat & tanggal surat, ubah status jadi selesai
    public function guruProses(Request $request, SuratKeterangan $surat)
    {
        $guru = Auth::guard('guru')->user();
        abort_unless($guru->isKurikulum(), 403);

        $data = $request->validate([
            'nomor_surat' => 'required|string|max:50',
            'tanggal_surat' => 'required|date',
        ]);

        $surat->update([
            'nomor_surat' => $data['nomor_surat'],
            'tanggal_surat' => $data['tanggal_surat'],
            'status' => 'selesai',
            'diproses_oleh' => $guru->id,
        ]);

        return back()->with('status', 'Surat berhasil diproses. Silakan cetak surat.');
    }

    public function guruTolak(Request $request, SuratKeterangan $surat)
    {
        $guru = Auth::guard('guru')->user();
        abort_unless($guru->isKurikulum(), 403);

        $data = $request->validate([
            'catatan' => 'required|string|max:500',
        ]);

        $surat->update([
            'status' => 'ditolak',
            'catatan' => $data['catatan'],
            'diproses_oleh' => $guru->id,
        ]);

        return back()->with('status', 'Pengajuan surat telah ditolak.');
    }

    // Tampilan cetak (PDF) surat keterangan, hanya untuk surat berstatus selesai
    public function cetak(SuratKeterangan $surat)
    {
        $guru = Auth::guard('guru')->user();
        abort_unless($guru->isKurikulum(), 403);
        abort_unless($surat->status === 'selesai', 404, 'Surat belum diproses.');

        return $this->buatPdf($surat);
    }

    
    // Dipakai oleh cetak() (guru) dan siswaCetak() (siswa) supaya hasilnya sama persis
    private function buatPdf(SuratKeterangan $surat)
    {
        $surat->load('siswa');

        $pdf = Pdf::loadView('guru.surat.cetak', compact('surat'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('surat-keterangan-' . $surat->siswa->nis . '.pdf');
    }
}