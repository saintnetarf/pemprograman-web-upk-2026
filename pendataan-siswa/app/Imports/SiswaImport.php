<?php

namespace App\Imports;

use App\Enums\Role;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Support\SiswaKolom;
use App\Support\SiswaRules;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToCollection, WithHeadingRow
{
    public const MAKS_BARIS = 1000;

    public array $hasil = [
        'dry_run' => false,
        'total' => 0,
        'dibuat' => 0,
        'diperbarui' => 0,
        'gagal' => 0,
        'galat' => [],
        'akun_baru' => [],
    ];

    private const PESAN = [
        'required' => 'Kolom :attribute wajib diisi.',
        'required_with' => 'Kolom :attribute wajib diisi jika :values terisi.',
        'digits' => 'Kolom :attribute harus terdiri dari :digits digit angka.',
        'integer' => 'Kolom :attribute harus berupa angka bulat.',
        'numeric' => 'Kolom :attribute harus berupa angka.',
        'between.numeric' => 'Kolom :attribute harus bernilai antara :min dan :max.',
        'min.numeric' => 'Kolom :attribute minimal bernilai :min.',
        'max.numeric' => 'Kolom :attribute maksimal bernilai :max.',
        'max.string' => 'Kolom :attribute maksimal :max karakter.',
        'in' => 'Nilai kolom :attribute tidak valid.',
        'email' => 'Format email pada kolom :attribute tidak valid.',
        'boolean' => 'Kolom :attribute harus bernilai Ya atau Tidak.',
        'unique' => ':attribute sudah terdaftar di sistem.',
        'string' => 'Kolom :attribute harus berupa teks.',
    ];

    public function __construct(private bool $periksaSaja = false)
    {
        $this->hasil['dry_run'] = $this->periksaSaja;
    }

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            $this->hasil['galat'][] = [
                'baris' => '-',
                'nama' => '-',
                'pesan' => ['Berkas tidak berisi data siswa.'],
            ];
            return;
        }

        // Periksa kolom template yang hilang
        $firstRow = $rows->first()->toArray();
        $missing = array_diff(SiswaKolom::kolom(), array_keys($firstRow));
        if (! empty($missing)) {
            $this->hasil['galat'][] = [
                'baris' => '-',
                'nama' => '-',
                'pesan' => [
                    'Kolom tidak ditemukan: ' . implode(', ', $missing) . '. Gunakan template terbaru dan jangan mengubah baris judul.',
                ],
            ];
            return;
        }

        // Buang baris kosong dan petakan nomor baris Excel (indeks + 2)
        $nonEmptyRows = [];
        foreach ($rows as $index => $row) {
            $nomor = $index + 2;
            $data = $row->toArray();

            $hasContent = false;
            foreach ($data as $val) {
                if ($val !== null && trim((string) $val) !== '') {
                    $hasContent = true;
                    break;
                }
            }

            if ($hasContent) {
                $nonEmptyRows[$nomor] = $data;
            }
        }

        if (empty($nonEmptyRows)) {
            $this->hasil['galat'][] = [
                'baris' => '-',
                'nama' => '-',
                'pesan' => ['Berkas tidak berisi data siswa.'],
            ];
            return;
        }

        if (count($nonEmptyRows) > self::MAKS_BARIS) {
            $this->hasil['total'] = count($nonEmptyRows);
            $this->hasil['gagal'] = count($nonEmptyRows);
            $this->hasil['galat'][] = [
                'baris' => '-',
                'nama' => '-',
                'pesan' => ['Jumlah data melebihi batas maksimal ' . self::MAKS_BARIS . ' baris.'],
            ];
            return;
        }

        $this->hasil['total'] = count($nonEmptyRows);

        // Peta kelas
        $kelasMap = Kelas::pluck('id', 'nama')->mapWithKeys(function ($id, $nama) {
            return [Str::lower(trim($nama)) => $id];
        })->all();

        $nikMuncul = [];
        $ortuDirencanakan = [];

        DB::transaction(function () use ($nonEmptyRows, $kelasMap, &$nikMuncul, &$ortuDirencanakan) {
            foreach ($nonEmptyRows as $nomor => $data) {
                $this->prosesBaris($nomor, $data, $kelasMap, $nikMuncul, $ortuDirencanakan);
            }
        });
    }

    private function prosesBaris(
        int $nomor,
        array $rawData,
        array $kelasMap,
        array &$nikMuncul,
        array &$ortuDirencanakan
    ): void {
        $pesan = [];
        $data = [];

        // 1. Normalisasi
        foreach (SiswaKolom::kolom() as $kolom) {
            $val = $rawData[$kolom] ?? null;

            if (is_string($val)) {
                $val = trim($val);
                if ($val === '') {
                    $val = null;
                }
            }

            $data[$kolom] = $val;
        }

        // NIK non-string
        $rawNik = $rawData['nik'] ?? null;
        if ($rawNik !== null && ! is_string($rawNik) && (is_int($rawNik) || is_float($rawNik))) {
            $pesan[] = 'NIK terbaca sebagai angka sehingga digit terakhirnya bisa berubah. Ubah format kolom menjadi Teks, lalu ketik ulang NIK.';
            $data['nik'] = sprintf('%.0f', $rawNik);
        }

        // Normalisasi tipe angka untuk kode_pos, no_hp, kelas
        foreach (['kode_pos', 'no_hp', 'kelas'] as $col) {
            if (isset($data[$col]) && (is_int($data[$col]) || is_float($data[$col]))) {
                $data[$col] = (string) $data[$col];
            }
        }

        // Normalisasi boolean pernah_tk dan pernah_paud
        foreach (['pernah_tk', 'pernah_paud'] as $boolCol) {
            if (isset($data[$boolCol])) {
                $str = Str::lower((string) $data[$boolCol]);
                if (in_array($str, ['ya', 'y', '1', 'true'], true)) {
                    $data[$boolCol] = 1;
                } elseif (in_array($str, ['tidak', 't', '0', 'false'], true)) {
                    $data[$boolCol] = 0;
                }
            }
        }

        // Normalisasi koordinat
        foreach (['latitude', 'longitude'] as $coordCol) {
            if (is_string($data[$coordCol])) {
                $data[$coordCol] = str_replace(',', '.', $data[$coordCol]);
            }
        }

        // Normalisasi email orang tua
        if (is_string($data['email_orang_tua'])) {
            $data['email_orang_tua'] = Str::lower($data['email_orang_tua']);
        }

        // 2. Pemeriksaan NIK kembar dalam berkas
        $nik = $data['nik'] ?? null;
        if ($nik) {
            if (isset($nikMuncul[$nik])) {
                $pesan[] = "NIK sama dengan baris {$nikMuncul[$nik]}.";
            } else {
                $nikMuncul[$nik] = $nomor;
            }
        }

        $siswa = $nik ? Siswa::where('nik', $nik)->first() : null;

        // Validasi aturan dasar
        $rules = SiswaRules::dasar($siswa?->id) + [
            'kelas' => ['required', 'string'],
            'nama_orang_tua' => ['nullable', 'string', 'max:255'],
            'email_orang_tua' => ['nullable', 'email', 'max:255'],
        ];

        $validator = Validator::make($data, $rules, self::PESAN);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $err) {
                $pesan[] = $err;
            }
        }

        // Validasi kelas
        $kelasNama = $data['kelas'] ?? null;
        $kelasKey = $kelasNama ? Str::lower(trim($kelasNama)) : null;
        $kelasId = $kelasKey && isset($kelasMap[$kelasKey]) ? $kelasMap[$kelasKey] : null;
        if ($kelasNama && ! $kelasId) {
            $pesan[] = "Kelas \"{$kelasNama}\" tidak ditemukan. Buat dulu pada menu Kelas.";
        }

        // Validasi akun orang tua
        $emailOrtu = $data['email_orang_tua'] ?? null;
        $namaOrtu = $data['nama_orang_tua'] ?? null;
        if ($emailOrtu) {
            $akunOrtu = User::where('email', $emailOrtu)->first();
            if ($akunOrtu) {
                if ($akunOrtu->role !== Role::OrangTua) {
                    $pesan[] = "Email {$emailOrtu} sudah dipakai akun dengan peran {$akunOrtu->role->label()}.";
                }
            } else {
                $sudahDirencanakan = isset($ortuDirencanakan[$emailOrtu]);
                if (! $sudahDirencanakan && empty($namaOrtu)) {
                    $pesan[] = "Nama orang tua wajib diisi karena email {$emailOrtu} belum terdaftar.";
                }
            }
        }

        // Bila ada galat, catat dan lewati
        if (! empty($pesan)) {
            $this->hasil['gagal']++;
            $this->hasil['galat'][] = [
                'baris' => $nomor,
                'nama' => $data['nama_lengkap'] ?? '-',
                'pesan' => $pesan,
            ];
            return;
        }

        // 3. Penyimpanan baris valid
        $atribut = Arr::only($data, array_keys(SiswaRules::dasar())) + [
            'kelas_id' => $kelasId,
        ];

        // Jika koordinat null, jangan timpa koordinat lama
        if ($data['latitude'] === null) {
            unset($atribut['latitude'], $atribut['longitude']);
        }

        if ($this->periksaSaja) {
            if ($emailOrtu && ! User::where('email', $emailOrtu)->exists()) {
                if (! isset($ortuDirencanakan[$emailOrtu])) {
                    $ortuDirencanakan[$emailOrtu] = true;
                    $this->hasil['akun_baru'][] = [
                        'nama' => $namaOrtu,
                        'email' => $emailOrtu,
                        'password' => null,
                    ];
                }
            }

            if ($siswa) {
                $this->hasil['diperbarui']++;
            } else {
                $this->hasil['dibuat']++;
            }
            return;
        }

        // Mode sungguhan
        $orangTuaId = null;
        if ($emailOrtu) {
            $akunOrtu = User::where('email', $emailOrtu)->first();
            if ($akunOrtu) {
                $orangTuaId = $akunOrtu->id;
            } else {
                $password = Str::password(10, symbols: false);
                $akunOrtu = new User([
                    'name' => $namaOrtu,
                    'email' => $emailOrtu,
                    'password' => $password,
                ]);
                $akunOrtu->role = Role::OrangTua;
                $akunOrtu->save();

                $orangTuaId = $akunOrtu->id;
                $this->hasil['akun_baru'][] = [
                    'nama' => $namaOrtu,
                    'email' => $emailOrtu,
                    'password' => $password,
                ];
            }
        }

        if ($orangTuaId) {
            $atribut['orang_tua_id'] = $orangTuaId;
        }

        if ($siswa) {
            $this->hasil['diperbarui']++;
        } else {
            $this->hasil['dibuat']++;
        }

        Siswa::updateOrCreate(['nik' => $nik], $atribut);
    }
}

