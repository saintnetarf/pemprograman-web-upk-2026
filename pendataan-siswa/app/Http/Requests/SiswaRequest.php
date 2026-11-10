<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Models\Siswa;
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

        $rules = [
            'nik' => ['required', 'digits:16', Rule::unique('siswas', 'nik')->ignore($id)],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'status_dalam_keluarga' => ['required', Rule::in(Siswa::STATUS_KELUARGA)],
            'anak_ke' => ['required', 'integer', 'min:1', 'max:30'],
            'jumlah_saudara' => ['required', 'integer', 'min:0', 'max:30'],
            'pernah_tk' => ['required', 'boolean'],
            'pernah_paud' => ['required', 'boolean'],
            'hobi' => ['required', 'string', 'max:100'],
            'cita_cita' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string'],
            'kabupaten_kota' => ['required', 'string', 'max:100'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'kelurahan' => ['required', 'string', 'max:100'],
            'kode_pos' => ['required', 'digits:5'],
            'status_tinggal' => ['required', Rule::in(Siswa::STATUS_TINGGAL)],
            'jarak_ke_sekolah' => ['required', Rule::in(Siswa::JARAK)],
            'transportasi' => ['required', Rule::in(Siswa::TRANSPORTASI)],
            'waktu_tempuh' => ['required', Rule::in(Siswa::WAKTU_TEMPUH)],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];

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
