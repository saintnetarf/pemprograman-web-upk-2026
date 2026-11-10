<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaTest extends TestCase
{
    use RefreshDatabase;

    private User $sekolah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sekolah = new User();
        $this->sekolah->forceFill([
            'name' => 'Operator Sekolah',
            'email' => 'sekolah@sekolah.test',
            'password' => 'password',
            'role' => Role::Sekolah,
        ]);
        $this->sekolah->save();
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'nik' => '6371000000000099',
            'nama_lengkap' => 'Test Siswa',
            'status_dalam_keluarga' => 'Anak Kandung',
            'anak_ke' => 1,
            'jumlah_saudara' => 2,
            'pernah_tk' => 1,
            'pernah_paud' => 1,
            'hobi' => 'Membaca',
            'cita_cita' => 'Guru',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Test No. 1',
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
        ], $overrides);
    }

    public function test_sekolah_can_view_siswa_index(): void
    {
        $response = $this->actingAs($this->sekolah)->get('/siswa');
        $response->assertStatus(200);
    }

    public function test_sekolah_can_create_siswa(): void
    {
        $payload = $this->validPayload();

        $response = $this->actingAs($this->sekolah)->post('/siswa', $payload);
        $response->assertRedirect('/siswa');
        $this->assertDatabaseHas('siswas', ['nik' => '6371000000000099']);
    }

    public function test_sekolah_can_update_siswa(): void
    {
        $siswa = Siswa::create($this->validPayload());

        $payload = $this->validPayload(['nama_lengkap' => 'Nama Baru']);

        $response = $this->actingAs($this->sekolah)->put("/siswa/{$siswa->id}", $payload);
        $response->assertRedirect('/siswa');
        $this->assertDatabaseHas('siswas', ['id' => $siswa->id, 'nama_lengkap' => 'Nama Baru']);
    }

    public function test_sekolah_can_delete_siswa(): void
    {
        $siswa = Siswa::create($this->validPayload());

        $response = $this->actingAs($this->sekolah)->delete("/siswa/{$siswa->id}");
        $response->assertRedirect('/siswa');
        $this->assertDatabaseMissing('siswas', ['id' => $siswa->id]);
    }

    public function test_sekolah_can_fetch_peta_data(): void
    {
        Siswa::create($this->validPayload());

        $response = $this->actingAs($this->sekolah)->getJson('/peta/data');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'type',
                'features' => [
                    '*' => [
                        'type',
                        'geometry' => ['type', 'coordinates'],
                        'properties' => [
                            'nama', 'kelas', 'kelurahan', 'kecamatan', 'jarak', 'transportasi', 'waktu_tempuh', 'edit_url'
                        ],
                    ],
                ],
            ]);
    }
}

