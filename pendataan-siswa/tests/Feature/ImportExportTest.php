<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Support\SiswaKolom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ImportExportTest extends TestCase
{
    use RefreshDatabase;

    private int $nikCounter = 0;

    private function akun(Role $role, ?string $email = null): User
    {
        $user = new User();
        $user->forceFill([
            'name' => $role->label() . ' ' . uniqid(),
            'email' => $email ?? (uniqid() . '@sekolah.test'),
            'password' => 'password',
            'role' => $role,
        ])->save();

        return $user;
    }

    private function baris(array $ubah = []): array
    {
        $base = array_combine(SiswaKolom::kolom(), array_column(SiswaKolom::DAFTAR, 3));

        return array_merge($base, $ubah);
    }

    private function xlsx(array $baris): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headings = SiswaKolom::kolom();
        foreach ($headings as $cIdx => $head) {
            $sheet->setCellValueExplicit([$cIdx + 1, 1], (string) $head, DataType::TYPE_STRING);
        }

        foreach ($baris as $rIdx => $rowValues) {
            $rowNum = $rIdx + 2;
            foreach ($headings as $cIdx => $colKey) {
                $val = $rowValues[$colKey] ?? '';
                $sheet->setCellValueExplicit([$cIdx + 1, $rowNum], (string) $val, DataType::TYPE_STRING);
            }
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'test_import_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return new UploadedFile(
            $tempFile,
            'siswa.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }

    private function siswa(array $ubah = []): Siswa
    {
        $this->nikCounter++;
        $nik = sprintf('6371%012d', $this->nikCounter);

        return Siswa::create(array_merge([
            'nik' => $nik,
            'nama_lengkap' => 'Siswa Test ' . $this->nikCounter,
            'status_dalam_keluarga' => 'Anak Kandung',
            'anak_ke' => 1,
            'jumlah_saudara' => 2,
            'pernah_tk' => true,
            'pernah_paud' => true,
            'hobi' => 'Membaca',
            'cita_cita' => 'Guru',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Pahlawan No. 10',
            'kabupaten_kota' => 'Kota Banjarmasin',
            'kecamatan' => 'Banjarmasin Tengah',
            'kelurahan' => 'Pasar Lama',
            'kode_pos' => '70111',
            'status_tinggal' => 'Tinggal Dengan Ayah dan Ibu',
            'jarak_ke_sekolah' => 'Kurang dari 5 KM',
            'transportasi' => 'Sepeda Motor',
            'waktu_tempuh' => '10-19 menit',
            'latitude' => -3.3248,
            'longitude' => 114.5911,
        ], $ubah));
    }

    private function impor(User $user, array $baris, bool $periksaSaja = false)
    {
        $file = $this->xlsx($baris);

        return $this->actingAs($user)->post(route('siswa.import.store'), [
            'file' => $file,
            'periksa_saja' => $periksaSaja ? '1' : '0',
        ]);
    }

    public function test_hak_akses_import(): void
    {
        $wali = $this->akun(Role::WaliKelas);
        $sekolah = $this->akun(Role::Sekolah);

        $file = $this->xlsx([$this->baris()]);

        $this->actingAs($wali)->get(route('siswa.import.create'))->assertStatus(403);
        $this->actingAs($wali)->get(route('siswa.import.template'))->assertStatus(403);
        $this->actingAs($wali)->post(route('siswa.import.store'), ['file' => $file])->assertStatus(403);

        $this->actingAs($sekolah)->get(route('siswa.import.create'))->assertStatus(200);
    }

    public function test_template(): void
    {
        $sekolah = $this->akun(Role::Sekolah);

        $res = $this->actingAs($sekolah)->get(route('siswa.import.template'));
        $res->assertDownload('template-import-siswa.xlsx');
    }

    public function test_import_sukses(): void
    {
        $sekolah = $this->akun(Role::Sekolah);
        $kelas = Kelas::create(['nama' => 'X-A']);

        $b1 = $this->baris([
            'nik' => '6371000000000101',
            'nama_lengkap' => 'Anak Satu',
            'kelas' => 'X-A',
            'email_orang_tua' => 'ortu@test.com',
            'nama_orang_tua' => 'Bapak Test',
        ]);
        $b2 = $this->baris([
            'nik' => '6371000000000102',
            'nama_lengkap' => 'Anak Dua',
            'kelas' => 'X-A',
            'email_orang_tua' => 'ortu@test.com',
            'nama_orang_tua' => '', // baris kedua tanpa nama_orang_tua
        ]);

        $res = $this->impor($sekolah, [$b1, $b2], false);
        $res->assertStatus(200);
        $res->assertSee('Import selesai');

        $this->assertDatabaseCount('siswas', 2);
        $this->assertDatabaseHas('siswas', ['nik' => '6371000000000101', 'kelas_id' => $kelas->id]);
        $this->assertDatabaseHas('siswas', ['nik' => '6371000000000102', 'kelas_id' => $kelas->id]);

        $ortuUsers = User::where('email', 'ortu@test.com')->get();
        $this->assertCount(1, $ortuUsers);
        $this->assertEquals(Role::OrangTua, $ortuUsers->first()->role);

        $s1 = Siswa::where('nik', '6371000000000101')->first();
        $s2 = Siswa::where('nik', '6371000000000102')->first();
        $this->assertEquals($ortuUsers->first()->id, $s1->orang_tua_id);
        $this->assertEquals($ortuUsers->first()->id, $s2->orang_tua_id);
    }

    public function test_periksa_saja(): void
    {
        $sekolah = $this->akun(Role::Sekolah);
        Kelas::create(['nama' => 'X-A']);

        $b1 = $this->baris([
            'nik' => '6371000000000101',
            'nama_lengkap' => 'Anak Satu',
            'kelas' => 'X-A',
            'email_orang_tua' => 'ortu@test.com',
            'nama_orang_tua' => 'Bapak Test',
        ]);

        $res = $this->impor($sekolah, [$b1], true);
        $res->assertStatus(200);
        $res->assertSee('belum ada data yang disimpan');

        $this->assertDatabaseEmpty('siswas');
        $this->assertDatabaseMissing('users', ['email' => 'ortu@test.com']);
    }

    public function test_baris_salah(): void
    {
        $sekolah = $this->akun(Role::Sekolah);
        Kelas::create(['nama' => 'X-A']);

        $b1 = $this->baris(['nik' => '6371000000000101', 'kelas' => 'X-A']);
        $b2 = $this->baris(['nik' => '6371000000000102', 'kelas' => 'Z-9']); // Kelas Z-9 tidak ada
        $b3 = $this->baris(['nik' => '6371000000000101', 'kelas' => 'X-A']); // NIK kembar

        $res = $this->impor($sekolah, [$b1, $b2, $b3], false);
        $res->assertStatus(200);
        $res->assertSee('tidak ditemukan');
        $res->assertSee('NIK sama dengan baris 2');

        $this->assertDatabaseCount('siswas', 1);
        $this->assertDatabaseHas('siswas', ['nik' => '6371000000000101']);
    }

    public function test_import_ulang(): void
    {
        $sekolah = $this->akun(Role::Sekolah);
        Kelas::create(['nama' => 'X-A']);

        $b1 = $this->baris([
            'nik' => '6371000000000101',
            'kelas' => 'X-A',
            'hobi' => 'Membaca',
            'email_orang_tua' => 'ortu@test.com',
            'nama_orang_tua' => 'Bapak Test',
        ]);
        $this->impor($sekolah, [$b1], false);

        $b1Update = $this->baris([
            'nik' => '6371000000000101',
            'kelas' => 'X-A',
            'hobi' => 'Menulis',
            'email_orang_tua' => 'ortu@test.com',
            'nama_orang_tua' => 'Bapak Test',
        ]);
        $this->impor($sekolah, [$b1Update], false);

        $this->assertDatabaseCount('siswas', 1);
        $this->assertDatabaseHas('siswas', ['nik' => '6371000000000101', 'hobi' => 'Menulis']);
        $this->assertDatabaseCount('users', 2); // sekolah + ortu
    }

    public function test_email_peran_lain(): void
    {
        $sekolah = $this->akun(Role::Sekolah);
        Kelas::create(['nama' => 'X-A']);

        $wali = $this->akun(Role::WaliKelas, 'wali@sekolah.test');

        $b1 = $this->baris([
            'nik' => '6371000000000101',
            'kelas' => 'X-A',
            'email_orang_tua' => 'wali@sekolah.test',
            'nama_orang_tua' => 'Pak Wali',
        ]);

        $res = $this->impor($sekolah, [$b1], false);
        $res->assertStatus(200);
        $res->assertSee('sudah dipakai akun dengan peran Wali Kelas');

        $this->assertDatabaseEmpty('siswas');
    }

    public function test_koordinat(): void
    {
        $sekolah = $this->akun(Role::Sekolah);
        Kelas::create(['nama' => 'X-A']);

        // 1. Latitude dan longitude kosong
        $b1 = $this->baris([
            'nik' => '6371000000000101',
            'kelas' => 'X-A',
            'latitude' => '',
            'longitude' => '',
        ]);
        $this->impor($sekolah, [$b1], false);

        $siswa = Siswa::where('nik', '6371000000000101')->first();
        $this->assertNull($siswa->latitude);
        $this->assertNull($siswa->longitude);

        // 2. Diisi manual
        $siswa->update([
            'latitude' => -3.333,
            'longitude' => 114.555,
        ]);

        // 3. Import ulang dengan koordinat kosong
        $this->impor($sekolah, [$b1], false);

        $siswaFresh = $siswa->fresh();
        $this->assertEquals(-3.333, $siswaFresh->latitude);
        $this->assertEquals(114.555, $siswaFresh->longitude);
    }

    public function test_ekspor_daftar(): void
    {
        $sekolah = $this->akun(Role::Sekolah);
        $wali = $this->akun(Role::WaliKelas);
        $ortu = $this->akun(Role::OrangTua);
        $userSiswa = $this->akun(Role::Siswa);

        // Sekolah dan wali kelas: 200 PDF
        $resSekolah = $this->actingAs($sekolah)->get(route('siswa.ekspor.daftar'));
        $resSekolah->assertStatus(200);
        $resSekolah->assertHeader('content-type', 'application/pdf');

        $resWali = $this->actingAs($wali)->get(route('siswa.ekspor.daftar'));
        $resWali->assertStatus(200);
        $resWali->assertHeader('content-type', 'application/pdf');

        // Orang tua dan siswa: 403
        $this->actingAs($ortu)->get(route('siswa.ekspor.daftar'))->assertStatus(403);
        $this->actingAs($userSiswa)->get(route('siswa.ekspor.daftar'))->assertStatus(403);
    }

    public function test_biodata(): void
    {
        $ortu = $this->akun(Role::OrangTua);
        $admin = $this->akun(Role::Admin);

        $anak = $this->siswa(['orang_tua_id' => $ortu->id]);
        $siswaLain = $this->siswa();

        // Orang tua: anak sendiri 200 PDF
        $resAnak = $this->actingAs($ortu)->get(route('siswa.ekspor.biodata', $anak));
        $resAnak->assertStatus(200);
        $resAnak->assertHeader('content-type', 'application/pdf');

        // Orang tua: siswa lain 403
        $this->actingAs($ortu)->get(route('siswa.ekspor.biodata', $siswaLain))->assertStatus(403);

        // Admin: 403
        $this->actingAs($admin)->get(route('siswa.ekspor.biodata', $anak))->assertStatus(403);
    }
}

