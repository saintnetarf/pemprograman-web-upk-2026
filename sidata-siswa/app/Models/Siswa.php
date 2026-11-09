<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
