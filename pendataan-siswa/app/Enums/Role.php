<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Sekolah = 'sekolah';
    case WaliKelas = 'wali_kelas';
    case OrangTua = 'orang_tua';
    case Siswa = 'siswa';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Sekolah => 'Sekolah',
            self::WaliKelas => 'Wali Kelas',
            self::OrangTua => 'Orang Tua',
            self::Siswa => 'Siswa',
        };
    }
}

