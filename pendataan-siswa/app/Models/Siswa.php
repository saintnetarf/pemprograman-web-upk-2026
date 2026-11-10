<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siswa extends Model
{
    // Pilihan isian yang sama dengan formulir pendataan
    public const STATUS_KELUARGA = [
        'Anak Kandung', 'Anak Tiri', 'Anak Angkat', 'Cucu', 'Famili Lain',
    ];

    public const STATUS_TINGGAL = [
        'Tinggal Dengan Ayah',
        'Tinggal Dengan Ibu',
        'Tinggal Dengan Ayah dan Ibu',
        'Tinggal dengan famili lain',
    ];

    public const JARAK = [
        'Kurang dari 5 KM', 'Antara 5 - 10 KM',
        'Antara 11 - 20 KM', 'Lebih dari 20 KM',
    ];

    public const TRANSPORTASI = [
        'Jalan Kaki', 'Sepeda Motor', 'Mobil', 'Antar Jemput',
    ];

    public const WAKTU_TEMPUH = [
        '1-10 menit', '10-19 menit', '20-29 menit', '30-39 menit', '> 40 menit',
    ];

    protected $fillable = [
        'kelas_id', 'user_id', 'orang_tua_id',
        'nik', 'nama_lengkap', 'status_dalam_keluarga', 'anak_ke',
        'jumlah_saudara', 'pernah_tk', 'pernah_paud', 'hobi', 'cita_cita',
        'no_hp', 'alamat', 'kabupaten_kota', 'kecamatan', 'kelurahan',
        'kode_pos', 'status_tinggal', 'jarak_ke_sekolah', 'transportasi',
        'waktu_tempuh', 'latitude', 'longitude',
    ];

    protected function casts(): array
    {
        return [
            'pernah_tk' => 'boolean',
            'pernah_paud' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function akun(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orangTua(): BelongsTo
    {
        return $this->belongsTo(User::class, 'orang_tua_id');
    }

    public function scopeDapatDilihat(Builder $query, User $user): Builder
    {
        return match ($user->role) {
            Role::Sekolah => $query,
            Role::WaliKelas => $query->whereIn(
                'kelas_id',
                Kelas::query()->where('wali_kelas_id', $user->id)->select('id')
            ),
            Role::OrangTua => $query->where('orang_tua_id', $user->id),
            Role::Siswa => $query->where('user_id', $user->id),
            default => $query->whereRaw('1 = 0'), // admin: tidak melihat data siswa
        };
    }

    public function scopeBerKoordinat(Builder $query): Builder
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    public function scopeCari(Builder $query, ?string $q): Builder
    {
        return $query->when($q, function ($query) use ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('nama_lengkap', 'like', "%{$q}%")
                  ->orWhere('nik', 'like', "%{$q}%")
                  ->orWhere('kecamatan', 'like', "%{$q}%");
            });
        });
    }

    public function scopeDiKelas(Builder $query, $kelasId): Builder
    {
        return $query->when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId));
    }
}
