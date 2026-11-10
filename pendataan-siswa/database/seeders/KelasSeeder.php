<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $wali1 = User::where('email', 'walikelas1@sekolah.test')->first();
        $wali2 = User::where('email', 'walikelas2@sekolah.test')->first();

        $kelasA = Kelas::updateOrCreate(
            ['nama' => 'X-A'],
            ['wali_kelas_id' => $wali1?->id]
        );

        $kelasB = Kelas::updateOrCreate(
            ['nama' => 'X-B'],
            ['wali_kelas_id' => $wali2?->id]
        );

        $siswa1 = User::where('email', 'siswa1@sekolah.test')->first();
        $siswa2 = User::where('email', 'siswa2@sekolah.test')->first();
        $ortu1 = User::where('email', 'ortu1@sekolah.test')->first();
        $ortu2 = User::where('email', 'ortu2@sekolah.test')->first();

        $mapping = [
            '6371000000000001' => ['kelas_id' => $kelasA->id, 'user_id' => $siswa1?->id, 'orang_tua_id' => $ortu1?->id],
            '6371000000000002' => ['kelas_id' => $kelasA->id, 'user_id' => $siswa2?->id, 'orang_tua_id' => $ortu2?->id],
            '6371000000000003' => ['kelas_id' => $kelasA->id, 'user_id' => null, 'orang_tua_id' => null],
            '6371000000000004' => ['kelas_id' => $kelasA->id, 'user_id' => null, 'orang_tua_id' => null],
            '6371000000000005' => ['kelas_id' => $kelasB->id, 'user_id' => null, 'orang_tua_id' => $ortu1?->id],
            '6371000000000006' => ['kelas_id' => $kelasB->id, 'user_id' => null, 'orang_tua_id' => null],
            '6371000000000007' => ['kelas_id' => $kelasB->id, 'user_id' => null, 'orang_tua_id' => null],
            '6371000000000008' => ['kelas_id' => $kelasB->id, 'user_id' => null, 'orang_tua_id' => null],
        ];

        foreach ($mapping as $nik => $data) {
            Siswa::where('nik', $nik)->update($data);
        }
    }
}

