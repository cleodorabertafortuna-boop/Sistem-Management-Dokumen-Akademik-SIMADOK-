<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesSpreadsheet;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SiswaImportController extends Controller
{
    use HandlesSpreadsheet;

    public function template()
    {
        return $this->unduhTemplate(
            'template-import-siswa.xlsx',
            ['nama', 'nis', 'kelas', 'password'],
            ['Budi Santoso', '1234567890', '10 RPL A', 'rahasia123'],
            ['B', 'D']
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

        set_time_limit(300); // hash password per baris butuh waktu

        $rows = $this->bacaBaris($request->file('file'));
        if (empty($rows)) {
            return back()->with('import_errors', ['File kosong atau tidak ada data di bawah header.']);
        }

        $kelasValid = array_flip($this->daftarKelas());
        $nisAda = Siswa::pluck('nis')->flip()->all();          // NIS yang sudah ada di DB
        $waliPerKelas = Guru::where('role', 'wali_kelas')
            ->pluck('id', 'kelas_dibina')->all();                    // ['10 RPL A' => 3, ...]

        $berhasil = 0;
        $errors = [];

        foreach ($rows as ['baris' => $baris, 'data' => $d]) {
            $nama = $d['nama'] ?? '';
            $nis = $d['nis'] ?? '';
            $kelas = strtoupper(preg_replace('/\s+/', ' ', $d['kelas'] ?? ''));
            $pass = $d['password'] ?? '';

            if ($nama === '' || $nis === '' || $kelas === '') {
                $errors[] = "Baris {$baris}: nama, NIS, dan kelas wajib diisi.";
                continue;
            }
            if (mb_strlen($nama) > 150 || mb_strlen($nis) > 30) {
                $errors[] = "Baris {$baris}: nama maksimal 150 karakter, NIS maksimal 30 karakter.";
                continue;
            }
            if (!isset($kelasValid[$kelas])) {
                $errors[] = "Baris {$baris}: kelas \"{$kelas}\" tidak valid (contoh: 10 RPL A).";
                continue;
            }
            if (isset($nisAda[$nis])) {
                $errors[] = "Baris {$baris}: NIS {$nis} sudah terdaftar.";
                continue;
            }
            if ($pass !== '' && mb_strlen($pass) < 6) {
                $errors[] = "Baris {$baris}: password minimal 6 karakter.";
                continue;
            }

            Siswa::create([
                'nama' => $nama,
                'nis' => $nis,
                'kelas' => $kelas,
                'guru_id' => $waliPerKelas[$kelas] ?? null,
                // kosong -> password awal = NIS (admin bisa menyuruh siswa menggantinya)
                'password' => Hash::make($pass !== '' ? $pass : $nis),
            ]);

            $nisAda[$nis] = true; // cegah NIS ganda di dalam file yang sama
            $berhasil++;
        }

        return back()
            ->with('success', "{$berhasil} siswa berhasil diimport.")
            ->with('import_errors', $errors);
    }
}