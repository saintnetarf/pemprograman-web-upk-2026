<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $hakAkses = match ($user->role) {
            Role::Admin => [
                'Mengelola akun pengguna sistem (menambah, mengubah, dan menghapus akun).',
                'Tidak memiliki akses ke data siswa maupun peta sebaran siswa.',
            ],
            Role::Sekolah => [
                'Mengelola data kelas dan penetapan wali kelas.',
                'Menambah, mengubah, menghapus, serta menautkan kelas dan akun siswa maupun orang tua.',
                'Melihat peta sebaran lokasi seluruh siswa.',
            ],
            Role::WaliKelas => [
                'Melihat dan mengubah data siswa di kelas yang Anda ampu.',
                'Melihat peta sebaran lokasi siswa di kelas yang Anda ampu.',
            ],
            Role::OrangTua => [
                'Melihat dan mengubah data anak Anda sendiri.',
                'Melihat lokasi anak Anda pada peta sebaran.',
            ],
            Role::Siswa => [
                'Melihat dan memperbarui data profil Anda sendiri.',
                'Melihat titik lokasi tempat tinggal Anda pada peta.',
            ],
        };

        return view('dashboard', compact('user', 'hakAkses'));
    }
}

