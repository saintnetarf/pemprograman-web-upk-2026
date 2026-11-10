<?php

namespace App\Support;

class SiswaKolom
{
    public const DAFTAR = [
        ['nik', true, 'NIK 16 digit siswa', '6371000000000101'],
        ['nama_lengkap', true, 'Nama lengkap siswa', 'Budi Santoso'],
        ['kelas', true, 'Nama kelas yang sudah terdaftar', 'X-A'],
        ['status_dalam_keluarga', true, 'Status dalam keluarga', 'Anak Kandung'],
        ['anak_ke', true, 'Anak ke (angka 1-30)', '1'],
        ['jumlah_saudara', true, 'Jumlah saudara kandung/tiri/angkat (angka 0-30)', '2'],
        ['pernah_tk', true, 'Pernah TK (Ya/Tidak)', 'Ya'],
        ['pernah_paud', true, 'Pernah PAUD (Ya/Tidak)', 'Tidak'],
        ['hobi', true, 'Hobi siswa (maks 100 karakter)', 'Membaca'],
        ['cita_cita', true, 'Cita-cita siswa (maks 100 karakter)', 'Guru'],
        ['no_hp', true, 'Nomor HP/telepon aktif (maks 20 karakter)', '081200000101'],
        ['alamat', true, 'Alamat domisili lengkap', 'Jl. Contoh No. 5 RT 01 RW 02'],
        ['kabupaten_kota', true, 'Kabupaten atau kota', 'Kota Banjarmasin'],
        ['kecamatan', true, 'Kecamatan', 'Banjarmasin Utara'],
        ['kelurahan', true, 'Kelurahan/desa', 'Sungai Miai'],
        ['kode_pos', true, 'Kode pos 5 digit', '70122'],
        ['status_tinggal', true, 'Status tempat tinggal', 'Tinggal Dengan Ayah dan Ibu'],
        ['jarak_ke_sekolah', true, 'Jarak tempat tinggal ke sekolah', 'Kurang dari 5 KM'],
        ['transportasi', true, 'Transportasi utama ke sekolah', 'Sepeda Motor'],
        ['waktu_tempuh', true, 'Waktu tempuh ke sekolah', '10-19 menit'],
        ['latitude', false, 'Titik koordinat latitude (derajat desimal)', '-3.2969'],
        ['longitude', false, 'Titik koordinat longitude (derajat desimal)', '114.5953'],
        ['nama_orang_tua', false, 'Nama orang tua/wali (wajib jika email baru)', 'Bapak Santoso'],
        ['email_orang_tua', false, 'Email akun orang tua untuk login', 'santoso@contoh.com'],
    ];

    public static function kolom(): array
    {
        return array_column(self::DAFTAR, 0);
    }
}

