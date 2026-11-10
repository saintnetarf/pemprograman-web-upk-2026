<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
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

    private function siswa(string $nama, array $relasi = []): Siswa
    {
        $this->nikCounter++;
        $nik = sprintf('6371%012d', $this->nikCounter);

        return Siswa::create(array_merge([
            'nik' => $nik,
            'nama_lengkap' => $nama,
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
        ], $relasi));
    }

    private function payload(Siswa $siswa, array $ubah = []): array
    {
        return array_merge([
            'nik' => $siswa->nik,
            'nama_lengkap' => $siswa->nama_lengkap,
            'status_dalam_keluarga' => $siswa->status_dalam_keluarga,
            'anak_ke' => $siswa->anak_ke,
            'jumlah_saudara' => $siswa->jumlah_saudara,
            'pernah_tk' => $siswa->pernah_tk ? 1 : 0,
            'pernah_paud' => $siswa->pernah_paud ? 1 : 0,
            'hobi' => $siswa->hobi,
            'cita_cita' => $siswa->cita_cita,
            'no_hp' => $siswa->no_hp,
            'alamat' => $siswa->alamat,
            'kabupaten_kota' => $siswa->kabupaten_kota,
            'kecamatan' => $siswa->kecamatan,
            'kelurahan' => $siswa->kelurahan,
            'kode_pos' => $siswa->kode_pos,
            'status_tinggal' => $siswa->status_tinggal,
            'jarak_ke_sekolah' => $siswa->jarak_ke_sekolah,
            'transportasi' => $siswa->transportasi,
            'waktu_tempuh' => $siswa->waktu_tempuh,
            'latitude' => $siswa->latitude,
            'longitude' => $siswa->longitude,
            'kelas_id' => $siswa->kelas_id,
            'user_id' => $siswa->user_id,
            'orang_tua_id' => $siswa->orang_tua_id,
        ], $ubah);
    }

    public function test_tamu_redirect_ke_login(): void
    {
        $this->get('/siswa')->assertRedirect('/login');
        $this->get('/peta/data')->assertRedirect('/login');
    }

    public function test_login(): void
    {
        $user = $this->akun(Role::Sekolah, 'operator@sekolah.test');

        // Password salah
        $resSalah = $this->from('/login')->post('/login', [
            'email' => 'operator@sekolah.test',
            'password' => 'passwordsalah',
        ]);
        $resSalah->assertRedirect('/login');
        $resSalah->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // Password benar
        $resBenar = $this->post('/login', [
            'email' => 'operator@sekolah.test',
            'password' => 'password',
        ]);
        $resBenar->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_akses(): void
    {
        $admin = $this->akun(Role::Admin);
        $sekolah = $this->akun(Role::Sekolah);

        // Admin ke /users dapat 200, ke /siswa dan /peta/data dapat 403
        $this->actingAs($admin)->get('/users')->assertStatus(200);
        $this->actingAs($admin)->get('/siswa')->assertStatus(403);
        $this->actingAs($admin)->get('/peta/data')->assertStatus(403);

        // Sekolah ke /users dapat 403
        $this->actingAs($sekolah)->get('/users')->assertStatus(403);
    }

    public function test_sekolah_melihat_semua_siswa(): void
    {
        $sekolah = $this->akun(Role::Sekolah);
        $this->siswa('Alpha Satu');
        $this->siswa('Beta Dua');

        $res = $this->actingAs($sekolah)->get('/siswa');
        $res->assertStatus(200);
        $res->assertSee('Alpha Satu');
        $res->assertSee('Beta Dua');
    }

    public function test_wali_kelas_akses_siswa(): void
    {
        $wali1 = $this->akun(Role::WaliKelas);
        $wali2 = $this->akun(Role::WaliKelas);

        $kelasA = Kelas::create(['nama' => 'X-A', 'wali_kelas_id' => $wali1->id]);
        $kelasB = Kelas::create(['nama' => 'X-B', 'wali_kelas_id' => $wali2->id]);

        $siswaA = $this->siswa('Alpha Satu', ['kelas_id' => $kelasA->id]);
        $siswaB = $this->siswa('Beta Dua', ['kelas_id' => $kelasB->id]);

        // Daftar hanya melihat kelasnya
        $res = $this->actingAs($wali1)->get('/siswa');
        $res->assertStatus(200);
        $res->assertSee('Alpha Satu');
        $res->assertDontSee('Beta Dua');

        // PUT siswa kelasnya sukses dan tersimpan
        $putData = $this->payload($siswaA, ['nama_lengkap' => 'Alpha Update']);
        $this->actingAs($wali1)->put("/siswa/{$siswaA->id}", $putData)
            ->assertRedirect('/siswa');
        $this->assertDatabaseHas('siswas', ['id' => $siswaA->id, 'nama_lengkap' => 'Alpha Update']);

        // Edit dan PUT siswa kelas lain dapat 403 dan data tidak berubah
        $this->actingAs($wali1)->get("/siswa/{$siswaB->id}/edit")->assertStatus(403);

        $putLain = $this->payload($siswaB, ['nama_lengkap' => 'Beta Diubah Wali Lain']);
        $this->actingAs($wali1)->put("/siswa/{$siswaB->id}", $putLain)->assertStatus(403);
        $this->assertDatabaseHas('siswas', ['id' => $siswaB->id, 'nama_lengkap' => 'Beta Dua']);
    }

    public function test_wali_kelas_mass_assignment(): void
    {
        $wali1 = $this->akun(Role::WaliKelas);
        $kelasA = Kelas::create(['nama' => 'X-A', 'wali_kelas_id' => $wali1->id]);
        $kelasB = Kelas::create(['nama' => 'X-B']);

        $siswaA = $this->siswa('Alpha Satu', ['kelas_id' => $kelasA->id]);

        // Wali kelas kirim PUT dengan kelas_id kelas lain
        $putData = $this->payload($siswaA, [
            'nama_lengkap' => 'Alpha Pindah',
            'kelas_id' => $kelasB->id,
        ]);

        $this->actingAs($wali1)->put("/siswa/{$siswaA->id}", $putData)
            ->assertRedirect('/siswa');

        // Nama terupdate, tetapi kelas_id di database TIDAK berubah
        $this->assertDatabaseHas('siswas', [
            'id' => $siswaA->id,
            'nama_lengkap' => 'Alpha Pindah',
            'kelas_id' => $kelasA->id,
        ]);
    }

    public function test_orang_tua_akses_anak(): void
    {
        $ortu = $this->akun(Role::OrangTua);
        $kelasA = Kelas::create(['nama' => 'X-A']);
        $kelasB = Kelas::create(['nama' => 'X-B']);

        $anak1 = $this->siswa('Alpha Satu', ['kelas_id' => $kelasA->id, 'orang_tua_id' => $ortu->id]);
        $anak2 = $this->siswa('Beta Dua', ['kelas_id' => $kelasB->id, 'orang_tua_id' => $ortu->id]);
        $bukanAnak = $this->siswa('Gamma Tiga');

        // Daftar hanya melihat anak (dua anak di kelas berbeda tetap terlihat)
        $res = $this->actingAs($ortu)->get('/siswa');
        $res->assertStatus(200);
        $res->assertSee('Alpha Satu');
        $res->assertSee('Beta Dua');
        $res->assertDontSee('Gamma Tiga');

        // Edit anak status 200
        $this->actingAs($ortu)->get("/siswa/{$anak1->id}/edit")->assertStatus(200);

        // Edit siswa lain status 403
        $this->actingAs($ortu)->get("/siswa/{$bukanAnak->id}/edit")->assertStatus(403);

        // DELETE anak sendiri dapat 403 dan data tetap ada
        $this->actingAs($ortu)->delete("/siswa/{$anak1->id}")->assertStatus(403);
        $this->assertDatabaseHas('siswas', ['id' => $anak1->id]);
    }

    public function test_siswa_akses_profil(): void
    {
        $userSiswa = $this->akun(Role::Siswa);
        $siswaA = $this->siswa('Alpha Satu', ['user_id' => $userSiswa->id]);
        $siswaB = $this->siswa('Beta Dua');

        // /profil redirect ke edit miliknya
        $this->actingAs($userSiswa)->get('/profil')
            ->assertRedirect(route('siswa.edit', $siswaA));

        // Edit milik sendiri status 200
        $this->actingAs($userSiswa)->get("/siswa/{$siswaA->id}/edit")->assertStatus(200);

        // Edit siswa lain status 403
        $this->actingAs($userSiswa)->get("/siswa/{$siswaB->id}/edit")->assertStatus(403);

        // /siswa/create status 403
        $this->actingAs($userSiswa)->get('/siswa/create')->assertStatus(403);
    }

    public function test_tambah_dan_hapus_siswa(): void
    {
        $wali = $this->akun(Role::WaliKelas);
        $sekolah = $this->akun(Role::Sekolah);

        // Wali kelas ke /siswa/create dapat 403, sekolah 200
        $this->actingAs($wali)->get('/siswa/create')->assertStatus(403);
        $this->actingAs($sekolah)->get('/siswa/create')->assertStatus(200);

        $siswa = $this->siswa('Delta Empat');

        // Wali kelas DELETE dapat 403
        $this->actingAs($wali)->delete("/siswa/{$siswa->id}")->assertStatus(403);
        $this->assertDatabaseHas('siswas', ['id' => $siswa->id]);

        // Sekolah DELETE sukses dan baris hilang
        $this->actingAs($sekolah)->delete("/siswa/{$siswa->id}")->assertRedirect('/siswa');
        $this->assertDatabaseMissing('siswas', ['id' => $siswa->id]);
    }

    public function test_peta_wali_kelas(): void
    {
        $wali = $this->akun(Role::WaliKelas);
        $kelasA = Kelas::create(['nama' => 'X-A', 'wali_kelas_id' => $wali->id]);
        $kelasB = Kelas::create(['nama' => 'X-B']);

        $this->siswa('Alpha Satu', ['kelas_id' => $kelasA->id]);
        $this->siswa('Beta Dua', ['kelas_id' => $kelasB->id]);

        $res = $this->actingAs($wali)->getJson('/peta/data');
        $res->assertStatus(200);

        $data = $res->json();
        $this->assertCount(1, $data['features']);

        $props = $data['features'][0]['properties'];
        $this->assertEquals('Alpha Satu', $props['nama']);
        $this->assertArrayNotHasKey('nik', $props);
        $this->assertArrayNotHasKey('no_hp', $props);
        $this->assertArrayNotHasKey('alamat', $props);
    }
}

