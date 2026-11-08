<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesSpreadsheet;
use App\Models\Guru;
use Illuminate\Http\Request;

class GuruImportController extends Controller
{
    use HandlesSpreadsheet;

    public function template()
    {
        return $this->unduhTemplate(
            'template-import-guru.xlsx',
            ['nama', 'nip', 'peran', 'kelas_dibina'],
            ['Siti Aminah, S.Pd', '198501012010012001', 'wali_kelas', '10 PPLG A'],
            ['B']
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes' => 'File harus berformat .xlsx, .xls, atau .csv.',
        ]);

        set_time_limit(300);

        $rows = $this->bacaBaris($request->file('file'));
        if (empty($rows)) {
            return back()->with('import_errors', ['File kosong atau tidak ada data di bawah header.']);
        }

        // Cek judul kolom dulu supaya penyebab gagal langsung kelihatan
        $kolomTerbaca = array_keys($rows[0]['data']);
        $kolomHilang = array_diff(['nama', 'nip', 'peran'], $kolomTerbaca);
        if (!empty($kolomHilang)) {
            return back()->with('import_errors', [
                'Judul kolom di baris 1 tidak sesuai. Kolom yang kurang: ' . implode(', ', $kolomHilang)
                . '. Kolom yang terbaca dari file: ' . implode(', ', $kolomTerbaca)
                . '. Baris 1 harus: nama, nip, peran, kelas_dibina.',
            ]);
        }

        $kelasValid = array_flip($this->daftarKelas());
        $nipAda = Guru::pluck('nip')->flip()->all();
        $kelasDipakai = Guru::where('role', 'wali_kelas')->pluck('kelas_dibina')->flip()->all();

        $berhasil = 0;
        $errors = [];

        foreach ($rows as ['baris' => $baris, 'data' => $d]) {
            $nama = $d['nama'] ?? '';
            // NIP disimpan tanpa spasi supaya cocok saat guru login
            $nip = preg_replace('/\s+/', '', $d['nip'] ?? '');
            $peran = strtolower(str_replace([' ', '-'], '_', $d['peran'] ?? ''));
            $kelas = strtoupper(preg_replace('/\s+/', ' ', $d['kelas_dibina'] ?? ''));

            if ($nama === '' || $nip === '') {
                $errors[] = "Baris {$baris}: nama dan NIP wajib diisi.";
                continue;
            }
            if (!in_array($peran, ['wali_kelas', 'kurikulum'], true)) {
                $errors[] = "Baris {$baris}: peran harus \"wali_kelas\" atau \"kurikulum\".";
                continue;
            }
            if (isset($nipAda[$nip])) {
                $errors[] = "Baris {$baris}: NIP {$nip} sudah terdaftar.";
                continue;
            }

            if ($peran === 'wali_kelas') {
                if (!isset($kelasValid[$kelas])) {
                    $errors[] = "Baris {$baris}: kelas \"{$kelas}\" tidak ada di daftar kelas (contoh yang valid: "
                        . implode(', ', array_slice(array_keys($kelasValid), 0, 5)) . ').';
                    continue;
                }
                if (isset($kelasDipakai[$kelas])) {
                    $errors[] = "Baris {$baris}: kelas {$kelas} sudah punya wali kelas.";
                    continue;
                }
                $kelasDipakai[$kelas] = true;
            } else {
                $kelas = null; // kurikulum tidak membina kelas
            }

            Guru::create([
                'nama' => $nama,
                'nip' => $nip,
                'role' => $peran,
                'kelas_dibina' => $kelas,
            ]);

            $nipAda[$nip] = true;
            $berhasil++;
        }

        return back()
            ->with('success', "{$berhasil} guru berhasil diimport.")
            ->with('import_errors', $errors);
    }
}