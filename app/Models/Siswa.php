<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Authenticatable
{
    protected $fillable = ['nama', 'nis', 'password', 'kelas', 'guru_id'];
    protected $hidden = ['password', 'remember_token'];

    protected static function booted(): void
    {
        // Wali kelas selalu mengikuti kelas siswa: saat siswa dibuat,
        // diimport, atau kelasnya diubah, guru_id dicari ulang dari kelas.
        static::saving(function (Siswa $siswa) {
            if (!$siswa->exists || $siswa->isDirty('kelas')) {
                $siswa->guru_id = Guru::where('role', 'wali_kelas')
                    ->where('kelas_dibina', $siswa->kelas)
                    ->value('id');
            }
        });
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function dokumens(): HasMany
    {
        return $this->hasMany(Dokumen::class);
    }

    public function raports()
    {
        return $this->dokumens()->where('kategori', 'raport')->orderBy('semester');
    }

    public function ijazah()
    {
        return $this->dokumens()->where('kategori', 'ijazah')->first();
    }

    public function sertifikats()
    {
        return $this->dokumens()->where('kategori', 'sertifikat');
    }
    public function suratKeterangans()
    {
        return $this->hasMany(\App\Models\SuratKeterangan::class);
    }
}