<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratKeterangan extends Model
{
    protected $fillable = [
        'siswa_id',
        'nomor_surat',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'nisn',
        'nama_ayah',
        'nama_ibu',
        'alamat',
        'tujuan_pengajuan',
        'status',
        'catatan',
        'tanggal_surat',
        'diproses_oleh',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_surat' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'diproses_oleh');
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'diajukan' => 'Menunggu Diproses',
            'diproses' => 'Sedang Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => $this->status,
        };
    }
}