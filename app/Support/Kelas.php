<?php

namespace App\Support;

class Kelas
{
    /** ['10' => ['10 TKP A', '10 DPIB A', ...], '11' => [...], '12' => [...]] */
    public static function grouped(): array
    {
        $hasil = [];

        foreach (config('kelas.angkatan') as $angkatan => $jurusans) {
            $hasil[$angkatan] = [];
            foreach ($jurusans as $jurusan => $jumlah) {
                for ($i = 0; $i < $jumlah; $i++) {
                    $hasil[$angkatan][] = "{$angkatan} {$jurusan} " . chr(65 + $i);
                }
            }
        }

        return $hasil;
    }

    /** Semua kelas dalam satu daftar datar. */
    public static function semua(): array
    {
        return array_merge(...array_values(self::grouped()));
    }

    public static function valid(?string $kelas): bool
    {
        return in_array($kelas, self::semua(), true);
    }
}