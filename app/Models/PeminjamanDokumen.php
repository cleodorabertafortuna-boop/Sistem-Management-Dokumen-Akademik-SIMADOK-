<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeminjamanDokumen extends Model
{
    protected $fillable = [
        'siswa_id',
        'tanggal_ambil',
        'dokumen',
        'alasan',
        'status',
        'catatan_guru',
        'guru_id',
        'diproses_at',
        'dikembalikan_at',
    ];

    protected $casts = [
        'tanggal_ambil' => 'date',
        'diproses_at' => 'datetime',
        'dikembalikan_at' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu konfirmasi',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'dikembalikan' => 'Sudah dikembalikan',
            default => $this->status,
        };
    }

    public function getWarnaStatusAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'bg-amber-50 text-amber-700',
            'disetujui' => 'bg-blue-50 text-blue-700',
            'ditolak' => 'bg-red-50 text-red-700',
            'dikembalikan' => 'bg-emerald-50 text-emerald-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }
}