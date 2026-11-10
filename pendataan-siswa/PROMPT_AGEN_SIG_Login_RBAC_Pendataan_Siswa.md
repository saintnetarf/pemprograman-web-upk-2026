# PROMPT AGEN: Login dan RBAC 5 Peran pada Aplikasi Pendataan Siswa SMA Berbasis Peta

> Prompt lanjutan dari `PROMPT_AGEN_SIG_Pendataan_Siswa_Laravel.md` (Praktikum 1 sampai 9).
> Jalankan di Claude Code (cloud atau lokal) pada folder yang berisi proyek `pendataan-siswa` hasil prompt pertama.
> Salin seluruh isi file ini sebagai instruksi, atau simpan sebagai `PROMPT_RBAC.md` lalu perintahkan:
> "Kerjakan seluruh instruksi pada PROMPT_RBAC.md dari awal sampai selesai."

---

## 1. Peran dan Tujuan

Kamu adalah software engineer Laravel senior. Pada proyek Laravel `pendataan-siswa` yang sudah ada (CRUD siswa + peta OpenStreetMap), tambahkan:

1. **Halaman login dan logout** (autentikasi bawaan Laravel, tanpa Breeze/Jetstream/Fortify).
2. **RBAC dengan 5 peran**: `admin`, `sekolah`, `wali_kelas`, `orang_tua`, `siswa`.
3. **Pembatasan data** pada daftar siswa, form, dan peta sesuai peran.

Ini adalah **kunci jawaban modul praktikum mahasiswa**. Kode harus **sederhana, rapi, berkomentar singkat dalam Bahasa Indonesia**. Jangan menambah fitur, paket, atau abstraksi di luar spesifikasi. Semua teks antarmuka dalam Bahasa Indonesia.

## 2. Hak Akses (spesifikasi utama)

| Fitur | Admin | Sekolah | Wali Kelas | Orang Tua | Siswa |
|---|---|---|---|---|---|
| Login dan logout | Ya | Ya | Ya | Ya | Ya |
| Tambah, ubah, hapus akun pengguna | Ya | - | - | - | - |
| Kelola data kelas dan wali kelas | - | Ya | - | - | - |
| Tambah siswa baru | - | Ya | - | - | - |
| Hapus siswa | - | Ya | - | - | - |
| Lihat dan ubah data siswa | - | Semua siswa | Siswa di kelas yang diampu | Anak sendiri | Diri sendiri |
| Atur kelas dan tautkan akun siswa dan orang tua | - | Ya | - | - | - |
| Lihat peta sebaran | - | Semua siswa | Siswa di kelasnya | Anak sendiri | Diri sendiri |

Keputusan desain yang **harus diikuti**:

- "Mengelola" bagi wali kelas, orang tua, dan siswa berarti **lihat dan ubah** saja. Tambah dan hapus siswa hanya sekolah.
- **Admin tidak dapat melihat data siswa** (daftar, form, maupun peta menghasilkan 403).
- Akun dibuat admin. Penautan (kelas siswa, akun siswa, akun orang tua) dan penetapan wali kelas dilakukan sekolah.
- Pengamanan berlapis: middleware `auth`, middleware `role` (halaman), **Policy** (satu data), **query scope** (daftar), **Form Request** (kolom yang boleh diubah).

## 3. Aturan Umum

- Gunakan stack yang sudah ada: Laravel 13 (PHP 8.3+), MySQL, Blade + Tailwind (Vite), Leaflet via CDN.
- Jangan memasang paket Composer atau npm baru.
- Kolom `role` **tidak boleh** masuk `$fillable` model `User`. Isi lewat penugasan langsung (`$user->role = ...`) atau `forceFill`.
- Nama file, class, route, dan kolom harus **persis** seperti spesifikasi.
- Gunakan data dummy saja.

## 4. Tahap Kerja (kerjakan berurutan)

### Tahap 0: Periksa kondisi proyek

1. Pastikan berada di folder proyek Laravel (ada `artisan`, `app/Models/Siswa.php`, `routes/web.php`). Jika proyek **belum ada**, hentikan dan laporkan bahwa prompt Praktikum 1 sampai 9 harus dijalankan lebih dulu.
2. Jalankan `php artisan test` dan catat hasil awal.
3. Periksa MySQL seperti pada prompt pertama (jalankan layanan bila tersedia). Jika MySQL tidak tersedia, verifikasi memakai SQLite (lihat Tahap 10), dan catat pada laporan akhir.

### Tahap 1: Enum Role dan kolom role (Praktikum 10)

1. `app/Enums/Role.php`: `enum Role: string` dengan case `Admin='admin'`, `Sekolah='sekolah'`, `WaliKelas='wali_kelas'`, `OrangTua='orang_tua'`, `Siswa='siswa'` dan method `label(): string` (Admin, Sekolah, Wali Kelas, Orang Tua, Siswa) memakai `match`.
2. Migration `add_role_to_users_table`: `string('role', 20)->default('siswa')->after('password')`; `down()` melakukan `dropColumn('role')`.
3. `app/Models/User.php`:
   - `casts()` menjadi `email_verified_at => datetime`, `password => hashed`, `role => Role::class`.
   - Method `hasRole(Role ...$roles): bool` memakai `in_array($this->role, $roles, true)`.
   - **Jangan** menambah `role` ke fillable (apa pun gaya fillable yang dipakai skeleton: properti `$fillable` atau atribut `#[Fillable]`).
4. `UserSeeder` (aman diulang, pakai `User::firstOrNew(['email'=>...])` lalu `forceFill([...name, password 'password', role...])->save()`). Delapan akun, password `password`:

| Nama | Email | Peran |
|---|---|---|
| Admin Sistem | admin@sekolah.test | admin |
| Operator Sekolah | sekolah@sekolah.test | sekolah |
| Bu Rina | walikelas1@sekolah.test | wali_kelas |
| Pak Dedi | walikelas2@sekolah.test | wali_kelas |
| Bapak Hasan | ortu1@sekolah.test | orang_tua |
| Ibu Rahmah | ortu2@sekolah.test | orang_tua |
| Ahmad Fauzi | siswa1@sekolah.test | siswa |
| Siti Aisyah | siswa2@sekolah.test | siswa |

5. `DatabaseSeeder::run()` memanggil `SiswaSeeder`, `UserSeeder` (dan `KelasSeeder` pada Tahap 4, urutan ini penting).

### Tahap 2: Login, logout, beranda (Praktikum 11)

1. `App\Http\Controllers\Auth\LoginController`:
   - `create()` menampilkan `auth.login`.
   - `store(Request)`: validasi `email` (required, email) dan `password` (required); `Auth::attempt($credentials, $request->boolean('remember'))`; bila gagal `back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email')`; bila berhasil `$request->session()->regenerate()` lalu `redirect()->intended(route('dashboard'))`.
   - `destroy(Request)`: `Auth::logout()`, `session()->invalidate()`, `session()->regenerateToken()`, redirect ke `login`.
2. `DashboardController` (invokable) mengirim `$user` dan `$hakAkses` (array teks per peran memakai `match` pada `Role`, tulis satu sampai tiga kalimat Bahasa Indonesia yang sesuai tabel hak akses) ke view `dashboard`.
3. View `auth/login.blade.php` (kartu di tengah, input email dan password memakai `<x-form-input>`, checkbox `remember`, tombol Masuk) dan `dashboard.blade.php` (salam, peran, daftar hak akses).
4. Layout `layouts/app.blade.php`: link brand ke `url('/')`; blok `@auth` berisi nav (Beranda, Peta, Data Siswa, nama dan peran pengguna, form **POST** Keluar dengan `@csrf`); tambah kotak flash `session('error')` (merah) di samping `success` (hijau).
5. Ganti `routes/web.php` menjadi:

```php
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/peta', [PetaController::class, 'index'])->name('peta');
    Route::get('/peta/data', [PetaController::class, 'data'])->name('peta.data');

    Route::resource('siswa', SiswaController::class)->except('show');
});
```

   Catatan: awalan `admin` dihapus (URL siswa menjadi `/siswa`), peta pindah ke `/peta`.
6. `bootstrap/app.php`, pada `withMiddleware`: `$middleware->redirectUsersTo('/dashboard');` (ubah hanya bagian itu).

### Tahap 3: Middleware role dan menu (Praktikum 12)

1. `App\Http\Middleware\EnsureRole`: `handle(Request $request, Closure $next, string ...$roles)`; bila tidak ada user atau `$user->role->value` tidak termasuk `$roles`, `abort(403)`.
2. Daftarkan alias di `bootstrap/app.php`: `$middleware->alias(['role' => \App\Http\Middleware\EnsureRole::class]);` (sambil mempertahankan `redirectUsersTo`).
3. `resources/views/errors/403.blade.php` (extends layout): "403 / Akses ditolak / Anda tidak memiliki hak akses ke halaman atau data ini." dan tombol **Kembali ke Beranda**.
4. Layout: tambah `@use('App\Enums\Role')` di baris pertama, `@php($user = auth()->user())` di dalam `@auth`, lalu menu bersyarat:
   - **Peta**: sekolah, wali_kelas, orang_tua, siswa.
   - **Data Siswa**: sekolah, wali_kelas, orang_tua.
   - **Profil Saya** (route `profil`): siswa.
   - **Kelas** (route `kelas.index`): sekolah.
   - **Pengguna** (route `users.index`): admin.
   - Beri komentar `{{-- MENU-ROLE --}}` sebagai penanda; tautan untuk route yang baru dibuat pada tahap berikutnya ditambahkan **setelah** route tersebut ada (jangan referensikan route yang belum didefinisikan).

### Tahap 4: Kelas dan relasi (Praktikum 13)

1. Migration `create_kelas_table` (buat dengan `php artisan make:model Kelas -m`). **Tulis nama tabel eksplisit `kelas`**: `id`, `string('nama', 30)->unique()`, `foreignId('wali_kelas_id')->nullable()->constrained('users')->nullOnDelete()`, timestamps.
2. Migration `add_relasi_to_siswas_table` (dibuat **setelah** migration kelas): `kelas_id` (nullable, constrained `kelas`, nullOnDelete, after id), `user_id` (nullable, **unique**, constrained `users`, nullOnDelete), `orang_tua_id` (nullable, constrained `users`, nullOnDelete). `down()` memakai `dropConstrainedForeignId`.
3. `App\Models\Kelas`: `protected $table = 'kelas'`, `$fillable = ['nama','wali_kelas_id']`, relasi `waliKelas()` (belongsTo User) dan `siswas()` (hasMany Siswa).
4. `Siswa`: tambah `kelas_id, user_id, orang_tua_id` ke `$fillable`; relasi `kelas()`, `akun()` (belongsTo User via `user_id`), `orangTua()`; dan **scope**:

```php
public function scopeDapatDilihat(Builder $query, User $user): Builder
{
    return match ($user->role) {
        Role::Sekolah => $query,
        Role::WaliKelas => $query->whereIn(
            'kelas_id',
            Kelas::query()->where('wali_kelas_id', $user->id)->select('id')
        ),
        Role::OrangTua => $query->where('orang_tua_id', $user->id),
        Role::Siswa => $query->where('user_id', $user->id),
        default => $query->whereRaw('1 = 0'), // admin: tidak melihat data siswa
    };
}
```

5. `User`: relasi `kelasDiampu()` (hasMany Kelas via `wali_kelas_id`), `siswaProfil()` (hasOne Siswa via `user_id`), `anak()` (hasMany Siswa via `orang_tua_id`).
6. `KelasSeeder` (aman diulang): kelas **X-A** (wali walikelas1) dan **X-B** (wali walikelas2) via `updateOrCreate`; lalu `Siswa::where('nik', $nik)->update([...])` dengan pemetaan NIK, kelas, akun siswa, akun orang tua:

| NIK | Siswa | Kelas | Akun siswa | Akun orang tua |
|---|---|---|---|---|
| 6371000000000001 | Ahmad Fauzi | X-A | siswa1 | ortu1 |
| 6371000000000002 | Siti Aisyah | X-A | siswa2 | ortu2 |
| 6371000000000003 | Muhammad Rizki | X-A | - | - |
| 6371000000000004 | Nur Halimah | X-A | - | - |
| 6371000000000005 | Rahmat Hidayat | X-B | - | ortu1 |
| 6371000000000006 | Dewi Lestari | X-B | - | - |
| 6371000000000007 | Aditya Pratama | X-B | - | - |
| 6371000000000008 | Putri Ramadhani | X-B | - | - |

   Catatan: `ortu1` memiliki dua anak di dua kelas berbeda (kasus uji penting).
7. Jalankan `php artisan migrate:fresh --seed`.

### Tahap 5: Admin kelola pengguna (Praktikum 14)

1. Komponen `components/form-select-relasi.blade.php`: props `name, label, options (id => teks), value=null, placeholder='-- Pilih --'`; opsi pertama value kosong; `@selected((string) old($name,$value) === (string) $id)`; menampilkan `@error`.
2. `UserRequest`: `authorize()` = `hasRole(Role::Admin)`; rules `name` (required, string, max:255), `email` (required, email, max:255, unique users kecuali `$this->route('user')?->id`), `role` (required, `Rule::enum(Role::class)`), `password` (`nullable` saat ubah, `required` saat tambah; string, min:8, confirmed).
3. `UserController` (index dengan pencarian `q` nama atau email + paginate 10; create; store; edit; update; destroy):
   - store: `new User($request->safe()->only(['name','email','password']))`, lalu `$user->role = $request->enum('role', Role::class)`, `save()`.
   - update: `fill(name, email)`, set role, ganti password hanya jika `filled('password')`.
   - destroy: tolak (flash `error`) bila `$user->is($request->user())`.
4. View `users/index`, `users/_form`, `users/create`, `users/edit` (tabel Nama, Email, Peran, Aksi; tombol Hapus tidak muncul untuk akun sendiri; peran dipilih dengan `x-form-select-relasi` dari `Role::cases()`).
5. Route di dalam grup `auth`: `Route::middleware('role:admin')->group(fn () => Route::resource('users', UserController::class)->except('show'));` dan menu **Pengguna** pada layout.

### Tahap 6: Sekolah kelola kelas (Praktikum 15)

1. `KelasController`: `index` (with waliKelas, withCount siswas, urut nama), `create`, `store`, `edit`, `update`, `destroy`.
   - Validasi inline: `nama` (required, string, max:30, unique `kelas,nama` kecuali dirinya), `wali_kelas_id` (nullable, `Rule::exists('users','id')->where('role', Role::WaliKelas->value)`).
   - destroy ditolak (flash `error`: "Kelas masih memiliki siswa. Pindahkan siswa terlebih dahulu.") bila masih ada siswa.
2. View `kelas/index` (kolom Kelas, Wali Kelas, Jumlah Siswa, Aksi) dan `kelas/form` (satu file untuk tambah dan ubah memakai `$kelas->exists`).
3. Route di dalam grup `auth`, **wajib** parameter eksplisit karena nama sumber daya "kelas":

```php
Route::middleware('role:sekolah')->group(function () {
    Route::resource('kelas', KelasController::class)
        ->parameters(['kelas' => 'kelas'])
        ->except('show');
});
```

   Tambahkan menu **Kelas** pada layout.

### Tahap 7: Policy, scope, controller, form (Praktikum 16)

1. `App\Policies\SiswaPolicy` (auto-discovery, tanpa registrasi manual):
   - `viewAny`: `hasRole(Sekolah, WaliKelas, OrangTua, Siswa)`.
   - `view` dan `update`: private `kelola($user, $siswa)`.
   - `create` dan `delete`: hanya `Role::Sekolah`.
   - `kelola` memakai `match ($user->role)`: Sekolah true; WaliKelas `(int) $siswa->kelas?->wali_kelas_id === $user->id`; OrangTua `(int) $siswa->orang_tua_id === $user->id`; Siswa `(int) $siswa->user_id === $user->id`; default false.
2. `SiswaRequest`:
   - `authorize()`: bila `$this->route('siswa')` ada, `$this->user()->can('update', $siswa)`; jika tidak, `$this->user()->can('create', Siswa::class)`.
   - `rules()`: simpan ke `$rules`; **hanya jika** `$this->user()->hasRole(Role::Sekolah)` tambahkan `kelas_id` (nullable, exists:kelas,id), `user_id` (nullable, exists users dengan role siswa, unique `siswas,user_id` kecuali dirinya), `orang_tua_id` (nullable, exists users dengan role orang_tua). Untuk peran lain, kunci ini tidak divalidasi sehingga tidak ikut `validated()`.
3. `SiswaController`:
   - Pakai `Gate::authorize(...)` (base `Controller` Laravel 11 ke atas tidak memiliki `authorize()`): `viewAny` di `index`, `create` di `create`, `update` di `edit`, `delete` di `destroy`. `store` dan `update` diotorisasi oleh `SiswaRequest::authorize()`.
   - `index`: `Siswa::query()->dapatDilihat($request->user())->with('kelas')`, pencarian dan paginasi seperti sebelumnya.
   - `profil(Request)`: ambil `$request->user()->siswaProfil`; `abort_if(! $siswa, 404, ...)`; redirect ke `siswa.edit`.
   - private `dataForm(Siswa $siswa)` mengirim `siswa`, `daftarKelas` (pluck nama,id), `akunSiswa`, `akunOrtu` ke view create dan edit.
4. Route: `Route::get('/profil', [SiswaController::class, 'profil'])->middleware('role:siswa')->name('profil');` di dalam grup `auth`, dan menu **Profil Saya**.
5. View `siswa/index`: tambah kolom **Kelas**; tombol Tambah dibungkus `@can('create', App\Models\Siswa::class)`, link Ubah `@can('update', $siswa)`, form Hapus `@can('delete', $siswa)`.
6. `siswa/_form`: sebelum tombol Simpan, tambah `fieldset` **Kelas dan Penautan Akun** (tiga `x-form-select-relasi`: `kelas_id`, `user_id`, `orang_tua_id`) yang hanya tampil bila `auth()->user()->hasRole(\App\Enums\Role::Sekolah)`.

### Tahap 8: Peta mengikuti hak akses (Praktikum 17)

1. `PetaController::index(Request)`: `Gate::authorize('viewAny', Siswa::class)`; kirim `total` = `Siswa::dapatDilihat($user)->count()`.
2. `PetaController::data(Request)`: `Gate::authorize('viewAny', Siswa::class)`; data = `Siswa::dapatDilihat($user)->with('kelas:id,nama')->get()`; GeoJSON `[longitude, latitude]`; `properties`: `nama, kelas (nama kelas atau '-'), kelurahan, kecamatan, jarak, transportasi, waktu_tempuh, edit_url` (`route('siswa.edit', $s)` bila `$user->can('update', $s)`, selain itu `null`). **NIK, no_hp, alamat tidak boleh ada.**
3. `peta/index.blade.php`: teks "Menampilkan N siswa sesuai hak akses Anda."; `fetch(route('peta.data'), { headers: { Accept: 'application/json' } })`; `if (!res.ok) throw`; popup menampilkan nama dan kelas, wilayah, jarak, transportasi, waktu tempuh, dan link **Ubah data** bila `edit_url` ada. Semua nilai tetap dilewatkan fungsi `esc()` (anti XSS).

### Tahap 9: Pembaruan test lama dan test RBAC (Praktikum 18)

1. **Test dari prompt pertama pasti terdampak** (URL `/admin/siswa` dan `/` kini berubah dan butuh login). Perbarui `tests/Feature/SiswaTest.php`: pakai URL baru (`/siswa`, `/peta/data`), login sebagai akun peran `sekolah` (`actingAs`), dan sesuaikan data valid (tanpa mengubah maksud test). Jangan menghapus test lama.
2. Pastikan `phpunit.xml` memakai `DB_CONNECTION=sqlite` dan `DB_DATABASE=:memory:` (tidak dikomentari), karena `RefreshDatabase` mengosongkan database.
3. Buat `tests/Feature/RbacTest.php` (PHPUnit, `RefreshDatabase`). Helper: `akun(Role)` (pakai `forceFill`), `siswa(nama, relasi)` (buat via `Siswa::create` dengan semua kolom valid dan NIK unik 16 digit), `payload(Siswa, ubah)` (data form valid untuk PUT). Gunakan **nama siswa unik** (Alpha Satu, Beta Dua, ...) agar `assertSee/assertDontSee` tidak tumpang tindih. Skenario wajib:

| Test | Yang diperiksa |
|---|---|
| tamu | `/siswa` dan `/peta/data` redirect ke `/login` |
| login | password salah: error session `email` dan tetap guest; benar: redirect `/dashboard` dan terautentikasi |
| admin | `/users` 200; `/siswa` dan `/peta/data` 403; sekolah ke `/users` 403 |
| sekolah | melihat semua siswa |
| wali kelas | daftar hanya kelasnya; PUT siswa kelasnya sukses dan tersimpan; edit dan PUT siswa kelas lain 403 dan data tidak berubah |
| wali kelas (mass assignment) | PUT dengan `kelas_id` kelas lain: redirect, tetapi `kelas_id` di DB tidak berubah |
| orang tua | daftar hanya anak (dua anak di kelas berbeda tetap terlihat); edit anak 200; edit siswa lain 403; DELETE anak sendiri 403 dan data tetap ada |
| siswa | `/profil` redirect ke edit miliknya; edit milik sendiri 200; edit siswa lain 403; `/siswa/create` 403 |
| tambah dan hapus | wali kelas ke `/siswa/create` 403; sekolah 200; wali kelas DELETE 403; sekolah DELETE sukses dan baris hilang |
| peta | wali kelas: `/peta/data` (getJson) 1 fitur sesuai kelasnya, `properties` tidak memuat `nik`, `no_hp`, `alamat` |

### Tahap 10: Verifikasi (wajib, jangan dilewati)

1. `php artisan migrate:fresh --seed` berhasil (pada MySQL jika tersedia).
2. `php artisan route:list --except-vendor` memuat `login`, `logout`, `dashboard`, `peta`, `peta.data`, `profil`, `siswa.*`, `users.*`, `kelas.*`.
3. `npm run build` sukses.
4. `php artisan test` **semua lulus** (test lama yang sudah diperbarui dan `RbacTest`). Perbaiki kode aplikasi, bukan melemahkan test.
5. Jalankan `php artisan serve` di latar belakang dan uji dengan `curl` memakai cookie jar (ambil token CSRF dari halaman `/login`, lalu `POST /login`):
   - Tanpa login, `GET /siswa` mengembalikan 302 ke `/login`.
   - Login `walikelas1@sekolah.test`: `GET /peta/data` mengembalikan tepat **4** fitur; `GET /users` mengembalikan **403**.
   - Login `ortu1@sekolah.test`: `/peta/data` mengembalikan **2** fitur.
   - Login `siswa1@sekolah.test`: `/peta/data` mengembalikan **1** fitur.
   - Login `admin@sekolah.test`: `/users` 200 dan `/peta/data` 403.
   Hentikan server setelah selesai.
6. Jika verifikasi memakai SQLite karena MySQL tidak tersedia, katakan dengan jelas pada laporan.

## 5. Batasan (jangan dilakukan)

- Jangan memasang Breeze, Jetstream, Fortify, Spatie Permission, atau paket otorisasi lain.
- Jangan menambah `role` ke `$fillable` dan jangan menerima `role`, `kelas_id`, `user_id`, atau `orang_tua_id` dari peran yang tidak berhak.
- Jangan mengirim `nik`, `no_hp`, atau `alamat` pada endpoint peta.
- Jangan membuat logout lewat GET. Logout harus POST dengan CSRF.
- Jangan membuat admin dapat melihat data siswa.
- Jangan mengubah nama kolom, route, atau konstanta yang sudah ada (kecuali perubahan URL yang tertulis pada Tahap 2).
- Jangan mereferensikan route yang belum didefinisikan pada layout.

## 6. Kriteria Selesai

- [ ] Tamu selalu diarahkan ke `/login`; login dan logout berfungsi; login dibatasi 5 percobaan per menit.
- [ ] Beranda menampilkan peran dan hak akses sesuai akun.
- [ ] Admin dapat menambah, mengubah, dan menghapus akun, tetapi tidak dapat membuka `/siswa` dan `/peta`.
- [ ] Sekolah dapat mengelola kelas, semua siswa, dan penautan akun.
- [ ] Wali kelas hanya melihat dan mengubah siswa kelasnya; orang tua hanya anaknya; siswa hanya dirinya.
- [ ] Membuka data milik orang lain lewat URL menghasilkan 403.
- [ ] Peta dan `/peta/data` mengikuti hak akses dan tidak membocorkan NIK, HP, alamat.
- [ ] `php artisan test` lulus semua dan `npm run build` sukses.

## 7. Laporan Akhir

Tampilkan ringkasan singkat: file baru dan file yang diubah, hasil `php artisan test`, hasil uji `curl` per akun, database yang dipakai saat verifikasi (MySQL atau SQLite), serta perintah menjalankan aplikasi:

```
php artisan migrate:fresh --seed
php artisan serve
npm run dev
```

Akun uji (password `password`): admin@sekolah.test, sekolah@sekolah.test, walikelas1@sekolah.test, walikelas2@sekolah.test, ortu1@sekolah.test, ortu2@sekolah.test, siswa1@sekolah.test, siswa2@sekolah.test.
