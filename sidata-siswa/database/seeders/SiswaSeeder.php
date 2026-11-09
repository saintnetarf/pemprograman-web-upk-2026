<?php

namespace Database\Seeders;

use App\Models\Siswa;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        // Nilai bawaan; setiap baris di bawah boleh menimpanya
        $default = [
            'status_dalam_keluarga' => 'Anak Kandung',
            'anak_ke' => 1,
            'jumlah_saudara' => 2,
            'pernah_tk' => true,
            'pernah_paud' => true,
            'kabupaten_kota' => 'Kota Banjarmasin',
            'status_tinggal' => 'Tinggal Dengan Ayah dan Ibu',
            'jarak_ke_sekolah' => 'Kurang dari 5 KM',
            'transportasi' => 'Sepeda Motor',
            'waktu_tempuh' => '10-19 menit',
        ];

        $rows = [
            ['nik' => '6371000000000001', 'nama_lengkap' => 'Ahmad Fauzi',
             'hobi' => 'Sepak bola', 'cita_cita' => 'Dokter', 'no_hp' => '081200000001',
             'alamat' => 'Jl. Sungai Miai Dalam No. 12 RT 05 RW 02',
             'kecamatan' => 'Banjarmasin Utara', 'kelurahan' => 'Sungai Miai',
             'kode_pos' => '70122', 'latitude' => -3.2969, 'longitude' => 114.5953],

            ['nik' => '6371000000000002', 'nama_lengkap' => 'Siti Aisyah',
             'hobi' => 'Membaca', 'cita_cita' => 'Guru', 'no_hp' => '081200000002',
             'alamat' => 'Jl. Pasar Lama No. 8 RT 03 RW 01',
             'kecamatan' => 'Banjarmasin Tengah', 'kelurahan' => 'Pasar Lama',
             'kode_pos' => '70111', 'latitude' => -3.3248, 'longitude' => 114.5911,
             'anak_ke' => 2, 'transportasi' => 'Jalan Kaki', 'waktu_tempuh' => '1-10 menit'],

            ['nik' => '6371000000000003', 'nama_lengkap' => 'Muhammad Rizki',
             'hobi' => 'Futsal', 'cita_cita' => 'Polisi', 'no_hp' => '081200000003',
             'alamat' => 'Jl. Kelayan A Gg. Mawar No. 21 RT 10 RW 04',
             'kecamatan' => 'Banjarmasin Selatan', 'kelurahan' => 'Kelayan Dalam',
             'kode_pos' => '70241', 'latitude' => -3.3467, 'longitude' => 114.5888,
             'jumlah_saudara' => 3, 'pernah_paud' => false],

            ['nik' => '6371000000000004', 'nama_lengkap' => 'Nur Halimah',
             'hobi' => 'Menggambar', 'cita_cita' => 'Arsitek', 'no_hp' => '081200000004',
             'alamat' => 'Jl. Pekapuran Raya No. 45 RT 07 RW 02',
             'kecamatan' => 'Banjarmasin Timur', 'kelurahan' => 'Pekapuran Raya',
             'kode_pos' => '70236', 'latitude' => -3.3138, 'longitude' => 114.6086,
             'jarak_ke_sekolah' => 'Antara 5 - 10 KM', 'transportasi' => 'Antar Jemput'],

            ['nik' => '6371000000000005', 'nama_lengkap' => 'Rahmat Hidayat',
             'hobi' => 'Memancing', 'cita_cita' => 'Pilot', 'no_hp' => '-',
             'alamat' => 'Jl. Teluk Dalam No. 3 RT 02 RW 01',
             'kecamatan' => 'Banjarmasin Barat', 'kelurahan' => 'Teluk Dalam',
             'kode_pos' => '70119', 'latitude' => -3.3236, 'longitude' => 114.5732,
             'transportasi' => 'Mobil', 'pernah_tk' => false],

            ['nik' => '6371000000000006', 'nama_lengkap' => 'Dewi Lestari',
             'hobi' => 'Menari', 'cita_cita' => 'Perawat', 'no_hp' => '081200000006',
             'alamat' => 'Jl. Sungai Lulut No. 17 RT 04 RW 03',
             'kecamatan' => 'Banjarmasin Timur', 'kelurahan' => 'Sungai Lulut',
             'kode_pos' => '70238', 'latitude' => -3.3011, 'longitude' => 114.6254,
             'jarak_ke_sekolah' => 'Antara 5 - 10 KM', 'waktu_tempuh' => '20-29 menit',
             'status_tinggal' => 'Tinggal Dengan Ibu'],

            ['nik' => '6371000000000007', 'nama_lengkap' => 'Aditya Pratama',
             'hobi' => 'Bermain game', 'cita_cita' => 'Programmer', 'no_hp' => '081200000007',
             'alamat' => 'Jl. A. Yani Km. 35 No. 9 RT 06 RW 02',
             'kabupaten_kota' => 'Kota Banjarbaru', 'kecamatan' => 'Banjarbaru Utara',
             'kelurahan' => 'Komet', 'kode_pos' => '70714',
             'latitude' => -3.4419, 'longitude' => 114.8320,
             'jarak_ke_sekolah' => 'Lebih dari 20 KM', 'transportasi' => 'Antar Jemput',
             'waktu_tempuh' => '> 40 menit'],

            ['nik' => '6371000000000008', 'nama_lengkap' => 'Putri Ramadhani',
             'hobi' => 'Bulu tangkis', 'cita_cita' => 'Atlet', 'no_hp' => '081200000008',
             'alamat' => 'Jl. Kayu Tangi No. 30 RT 09 RW 05',
             'kecamatan' => 'Banjarmasin Utara', 'kelurahan' => 'Alalak Selatan',
             'kode_pos' => '70124', 'latitude' => -3.2855, 'longitude' => 114.5860,
             'anak_ke' => 3, 'jumlah_saudara' => 4,
             'jarak_ke_sekolah' => 'Antara 5 - 10 KM'],
        ];

        foreach ($rows as $row) {
            // updateOrCreate membuat seeder aman dijalankan berulang kali
            Siswa::updateOrCreate(
                ['nik' => $row['nik']],
                array_merge($default, $row)
            );
        }
    }
}

