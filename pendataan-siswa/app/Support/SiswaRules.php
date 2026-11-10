<?php

namespace App\Support;

use App\Models\Siswa;
use Illuminate\Validation\Rule;

class SiswaRules
{
    public static function dasar(?int $ignoreId = null): array
    {
        return [
            'nik' => ['required', 'digits:16', Rule::unique('siswas', 'nik')->ignore($ignoreId)],
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
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
        ];
    }
}

