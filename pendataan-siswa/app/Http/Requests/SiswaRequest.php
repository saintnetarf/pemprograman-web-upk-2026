<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Models\Siswa;
use App\Support\SiswaRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $siswa = $this->route('siswa');

        return $siswa
            ? (bool) $this->user()?->can('update', $siswa)
            : (bool) $this->user()?->can('create', Siswa::class);
    }

    public function rules(): array
    {
        // Saat edit, NIK milik siswa itu sendiri dikecualikan dari cek unik
        $id = $this->route('siswa')?->id;
        $rules = SiswaRules::dasar($id);

        // Hanya sekolah yang boleh mengubah kelas_id, user_id, dan orang_tua_id
        if ($this->user()?->hasRole(Role::Sekolah)) {
            $rules['kelas_id'] = ['nullable', 'exists:kelas,id'];
            $rules['user_id'] = [
                'nullable',
                Rule::exists('users', 'id')->where('role', Role::Siswa->value),
                Rule::unique('siswas', 'user_id')->ignore($id),
            ];
            $rules['orang_tua_id'] = [
                'nullable',
                Rule::exists('users', 'id')->where('role', Role::OrangTua->value),
            ];
        }

        return $rules;
    }
}
