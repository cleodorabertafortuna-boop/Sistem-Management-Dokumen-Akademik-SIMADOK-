<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dokumen extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'siswa_id',
        'kategori',
        'jenis_sertifikat',
        'semester',
        'nama_file',
        'file_path',
        'uploaded_by',
        'uploaded_by_admin_id',
        'kelas_saat_unggah',
        'diunggah_oleh_nama',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    // Guru pengunggah (untuk dokumen raport & ijazah)
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'uploaded_by');
    }

    // Admin pengunggah (khusus dokumen sertifikat)
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'uploaded_by_admin_id');
    }

    // Nama pengunggah: guru/admin yang masih ada, atau nama yang tersimpan saat unggah
    // (tetap tampil walau akun guru sudah dihapus)
    public function getNamaPengunggahAttribute(): string
    {
        if ($this->guru) {
            return $this->guru->nama;
        }
        if ($this->admin) {
            return $this->admin->nama . ' (Admin)';
        }
        return $this->diunggah_oleh_nama ?: '-';
    }
}
