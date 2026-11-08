<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait HandlesSpreadsheet
{
    /** Daftar kelas valid, sama persis dengan dropdown di form. */
    protected function daftarKelas(): array
    {
        $list = [];
        foreach ([10, 11, 12] as $a) {
            foreach (['RPL', 'TM', 'TO', 'DPIB', 'TE'] as $j) {
                foreach (['A', 'B', 'C', 'D'] as $r) {
                    $list[] = "{$a} {$j} {$r}";
                }
            }
        }
        return \App\Support\Kelas::semua();
    }

    /** Baca file menjadi array baris [nomor_baris, data berdasarkan header]. */
    protected function bacaBaris(UploadedFile $file): array
    {
        $sheet = IOFactory::load($file->getRealPath())->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        $header = array_map(
            fn($h) => strtolower(trim((string) $h)),
            array_shift($rows) ?? []
        );

        $hasil = [];
        foreach ($rows as $i => $row) {
            $data = [];
            foreach ($header as $k => $nama) {
                if ($nama !== '') {
                    $data[$nama] = trim((string) ($row[$k] ?? ''));
                }
            }
            // lewati baris kosong
            if (count(array_filter($data, fn($v) => $v !== '')) === 0) {
                continue;
            }
            $hasil[] = ['baris' => $i + 2, 'data' => $data]; // +2: header = baris 1
        }
        return $hasil;
    }

    /** Download template .xlsx. $contoh = baris contoh, $kolomTeks = kolom yang dipaksa format teks. */
    protected function unduhTemplate(string $namaFile, array $header, array $contoh, array $kolomTeks = []): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($header, null, 'A1');
        $sheet->fromArray($contoh, null, 'A2');

        $lastCol = chr(64 + count($header));
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // NIS/NIP/password harus teks supaya angka panjang tidak jadi 1.2E+10
        foreach ($kolomTeks as $col) {
            $sheet->getStyle("{$col}2:{$col}1000")->getNumberFormat()->setFormatCode('@');
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}