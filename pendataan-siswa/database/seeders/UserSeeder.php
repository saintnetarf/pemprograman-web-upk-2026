<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin Sistem', 'email' => 'admin@sekolah.test', 'role' => Role::Admin],
            ['name' => 'Operator Sekolah', 'email' => 'sekolah@sekolah.test', 'role' => Role::Sekolah],
            ['name' => 'Bu Rina', 'email' => 'walikelas1@sekolah.test', 'role' => Role::WaliKelas],
            ['name' => 'Pak Dedi', 'email' => 'walikelas2@sekolah.test', 'role' => Role::WaliKelas],
            ['name' => 'Bapak Hasan', 'email' => 'ortu1@sekolah.test', 'role' => Role::OrangTua],
            ['name' => 'Ibu Rahmah', 'email' => 'ortu2@sekolah.test', 'role' => Role::OrangTua],
            ['name' => 'Ahmad Fauzi', 'email' => 'siswa1@sekolah.test', 'role' => Role::Siswa],
            ['name' => 'Siti Aisyah', 'email' => 'siswa2@sekolah.test', 'role' => Role::Siswa],
        ];

        foreach ($users as $data) {
            $user = User::firstOrNew(['email' => $data['email']]);
            $user->forceFill([
                'name' => $data['name'],
                'password' => 'password',
                'role' => $data['role'],
            ])->save();
        }
    }
}

