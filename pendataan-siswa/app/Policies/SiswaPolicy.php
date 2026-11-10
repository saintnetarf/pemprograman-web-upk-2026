<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Siswa;
use App\Models\User;

class SiswaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::Sekolah, Role::WaliKelas, Role::OrangTua, Role::Siswa);
    }

    public function view(User $user, Siswa $siswa): bool
    {
        return $this->kelola($user, $siswa);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::Sekolah);
    }

    public function update(User $user, Siswa $siswa): bool
    {
        return $this->kelola($user, $siswa);
    }

    public function delete(User $user, Siswa $siswa): bool
    {
        return $user->hasRole(Role::Sekolah);
    }

    private function kelola(User $user, Siswa $siswa): bool
    {
        return match ($user->role) {
            Role::Sekolah => true,
            Role::WaliKelas => (int) $siswa->kelas?->wali_kelas_id === $user->id,
            Role::OrangTua => (int) $siswa->orang_tua_id === $user->id,
            Role::Siswa => (int) $siswa->user_id === $user->id,
            default => false,
        };
    }
}

