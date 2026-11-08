<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Authenticatable
{
    protected $fillable = ['nama', 'nip', 'role', 'password', 'kelas_dibina'];
    protected $hidden = ['password', 'remember_token'];

    protected static function booted(): void
    {
        // Guru baru ditambah/diimport, atau peran/kelas binaannya berubah:
        // siswa lama dilepas, lalu seluruh siswa di kelas binaan dipasang ke guru ini.
        // Memakai query builder (bukan model) agar tidak memicu event Siswa lagi.
        static::saved(function (Guru $guru) {
            if (!$guru->wasRecentlyCreated && !$guru->wasChanged(['role', 'kelas_dibina'])) {
                return;
            }

            Siswa::where('guru_id', $guru->id)->update(['guru_id' => null]);

            if ($guru->role === 'wali_kelas' && $guru->kelas_dibina) {
                Siswa::where('kelas', $guru->kelas_dibina)->update(['guru_id' => $guru->id]);
            }
        });

        // Guru dihapus: siswa binaannya kembali tanpa wali kelas
        static::deleting(function (Guru $guru) {
            Siswa::where('guru_id', $guru->id)->update(['guru_id' => null]);
        });
    }

    // Siswa-siswa dalam kelas yang dibina guru ini (khusus role wali_kelas)
    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class, 'guru_id');
    }

    public function dokumenDiunggah(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'uploaded_by');
    }

    public function isWaliKelas(): bool
    {
        return $this->role === 'wali_kelas';
    }

    public function isKurikulum(): bool
    {
        return $this->role === 'kurikulum';
    }

    public function getLabelRoleAttribute(): string
    {
        return $this->role === 'kurikulum' ? 'Kurikulum' : 'Wali Kelas';
    }
}