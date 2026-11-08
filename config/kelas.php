<?php

// Jumlah rombel per jurusan. Rombel otomatis berhuruf: 1 = A, 2 = A-B, 3 = A-C, dst.
$kelas10 = [
    'TKP' => 1,
    'DPIB' => 2,
    'TE' => 5,
    'TO' => 5,
    'TK' => 2,
    'TM' => 4,
    'PPLG' => 3,
];

// Kelas 11 dan 12 memakai format yang sama.
$kelas11dan12 = [
    'TKP' => 1,
    'DPIB' => 2,
    'TEI' => 3,
    'TOI' => 2,
    'TKR' => 3,
    'TSM' => 2,
    'TITL' => 2,
    'TM' => 4,
    'TP' => 4,
    'TK' => 2,
    'RPL' => 3,
];

return [
    'angkatan' => [
        10 => $kelas10,
        11 => $kelas11dan12,
        12 => $kelas11dan12,
    ],
];