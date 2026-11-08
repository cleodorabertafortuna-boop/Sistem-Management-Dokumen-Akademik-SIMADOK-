<?php

namespace App\Http\Controllers;

use App\Models\PeminjamanDokumen;
use App\Models\SuratKeterangan;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    public function dashboard()
    {
        $siswa = Auth::guard('siswa')->user();

        $raports = $siswa->raports()->with('guru')->get();
        $ijazah = $siswa->dokumens()->where('kategori', 'ijazah')->with('guru')->first();
        $sertifikats = $siswa->sertifikats()->with('guru')->get()->groupBy('jenis_sertifikat');
        $riwayat = $siswa->dokumens()->with('guru')->latest()->take(5)->get();

        // Notifikasi hasil konfirmasi guru kurikulum
        $batas = now()->subDays(7);

        $notifPeminjaman = PeminjamanDokumen::where('siswa_id', $siswa->id)
            ->where(function ($q) use ($batas) {
                $q->where('status', 'disetujui')
                    ->orWhere(fn($w) => $w->where('status', 'ditolak')->where('diproses_at', '>=', $batas));
            })
            ->latest('diproses_at')
            ->get();

        $notifSurat = SuratKeterangan::where('siswa_id', $siswa->id)
            ->whereIn('status', ['selesai', 'ditolak'])
            ->where('updated_at', '>=', $batas)
            ->latest('updated_at')
            ->get();

        return view('siswa.dashboard', compact(
            'siswa',
            'raports',
            'ijazah',
            'sertifikats',
            'riwayat',
            'notifPeminjaman',
            'notifSurat'
        ));
    }
}