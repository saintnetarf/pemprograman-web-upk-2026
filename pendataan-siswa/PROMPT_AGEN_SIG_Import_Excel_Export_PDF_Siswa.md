# PROMPT AGEN: Import Excel dan Ekspor PDF Data Siswa (Laravel + Laravel Excel + DomPDF)

> Prompt lanjutan dari dua prompt sebelumnya:
> 1. `PROMPT_AGEN_SIG_Pendataan_Siswa_Laravel.md` (Praktikum 1 sampai 9)
> 2. `PROMPT_AGEN_SIG_Login_RBAC_Pendataan_Siswa.md` (Praktikum 10 sampai 18)
>
> Jalankan di Claude Code (cloud atau lokal) pada folder proyek `pendataan-siswa` yang **sudah memiliki login dan RBAC**.
> Salin seluruh isi file ini sebagai instruksi, atau simpan sebagai `PROMPT_IMPORT_EXPORT.md` lalu perintahkan:
> "Kerjakan seluruh instruksi pada PROMPT_IMPORT_EXPORT.md dari awal sampai selesai."

---

## 1. Peran dan Tujuan

Kamu adalah software engineer Laravel senior. Pada proyek `pendataan-siswa` yang sudah ada (CRUD siswa, peta OSM, login, RBAC 5 peran: `admin`, `sekolah`, `wali_kelas`, `orang_tua`, `siswa`), tambahkan:

1. **Import Excel** data siswa: input massal, **menempatkan siswa ke kelas**, serta **membuat dan menautkan akun orang tua**.
2. **Template Excel** yang dapat diunduh (judul berwarna, format teks, dropdown, sheet petunjuk).
3. **Ekspor PDF**: daftar siswa (dengan filter kelas dan pencarian) dan biodata satu siswa.

Ini adalah **kunci jawaban modul praktikum mahasiswa**. Kode harus **sederhana, rapi, berkomentar singkat dalam Bahasa Indonesia**. Jangan menambah fitur atau paket di luar spesifikasi. Semua teks antarmuka dalam Bahasa Indonesia.

## 2. Hak Akses (spesifikasi utama)

| Fitur | Admin | Sekolah | Wali Kelas | Orang Tua | Siswa |
|---|---|---|---|---|---|
| Unduh template dan import Excel | - | Ya | - | - | - |
| Ekspor daftar siswa ke PDF | - | Semua siswa | Siswa di kelas yang diampu | - | - |
| Unduh biodata siswa (PDF) | - | Semua siswa | Siswa di kelasnya | Anak sendiri | Diri sendiri |

Seluruh pembatasan memakai `SiswaPolicy` dan scope `dapatDilihat` yang sudah ada. Admin tidak pernah melihat data siswa.

## 3. Aturan Umum

- Paket baru yang **diizinkan hanya dua**: `maatwebsite/excel` dan `barryvdh/laravel-dompdf` (keduanya mendukung Laravel 13). Jangan memasang paket lain.
- Nama file, class, route, dan kolom harus **persis** seperti spesifikasi.
- `role` tetap **tidak boleh** masuk `$fillable` model `User`. Isi dengan `$akun->role = Role::OrangTua`.
- Jangan mengirim `nik`, `no_hp`, atau `alamat` pada endpoint peta (aturan lama tetap berlaku).
- Gunakan data dummy saja.

## 4. Tahap Kerja (kerjakan berurutan)

### Tahap 0: Periksa kondisi proyek

1. Pastikan berada pada proyek Laravel yang memiliki `artisan`, `app/Enums/Role.php`, `app/Policies/SiswaPolicy.php`, dan tabel `kelas`. Jika **tidak ada**, hentikan dan laporkan bahwa prompt Praktikum 1 sampai 18 harus dijalankan lebih dulu.
2. Jalankan `php artisan test` dan catat hasil awal (harus lulus).
3. Periksa ekstensi PHP: `php -m` harus memuat `zip`, `gd`, `xml`, `mbstring`, `fileinfo`. Jika `zip` atau `gd` tidak ada, coba pasang/aktifkan (misalnya `apt-get install php-zip php-gd` bila tersedia). Jika tidak bisa, hentikan dan laporkan.
4. Periksa `memory_limit` PHP (minimal 256M). Bila lebih kecil, gunakan `php -d memory_limit=512M` saat menjalankan composer, artisan, dan test, lalu catat pada laporan.
5. Periksa MySQL seperti prompt sebelumnya. Jika tidak tersedia, verifikasi memakai SQLite dan catat pada laporan akhir.

### Tahap 1: Pasang paket (Praktikum 19)

```
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

Jangan mempublikasikan konfigurasi. Uji dengan Tinker: `class_exists(Maatwebsite\Excel\Facades\Excel::class)` dan `class_exists(Barryvdh\DomPDF\Facade\Pdf::class)` harus `true`.

### Tahap 2: Koordinat opsional (Praktikum 20)

1. Migration `ubah_koordinat_siswas_nullable`: `decimal('latitude', 10, 7)->nullable()->change()` dan idem `longitude`. `down()` mengembalikan `nullable(false)` (beri komentar bahwa gagal bila ada baris tanpa koordinat).
2. Model `Siswa`: scope `scopeBerKoordinat(Builder $query)` = `whereNotNull('latitude')->whereNotNull('longitude')`.
3. `PetaController::index(Request)`: `$dilihat = Siswa::dapatDilihat($request->user())`; kirim `total` = `(clone $dilihat)->berKoordinat()->count()` dan `tanpaTitik` = `(clone $dilihat)->whereNull('latitude')->count()`.
4. `PetaController::data`: tambahkan `->berKoordinat()` setelah `dapatDilihat($user)`.
5. `peta/index.blade.php`: paragraf informasi menampilkan jumlah siswa; bila `$tanpaTitik > 0`, tambahkan teks amber "N siswa belum memiliki titik koordinat."
6. Daftar siswa: bila `latitude` null tampilkan lencana amber "Belum ada titik", selain itu `lat, lng`.

### Tahap 3: Aturan validasi bersama (Praktikum 21)

1. Buat `App\Support\SiswaRules` dengan `public static function dasar(?int $ignoreId = null): array` yang berisi **seluruh aturan data siswa** (persis seperti `SiswaRequest::rules()` sebelumnya): nik (required, digits:16, unique `siswas,nik` ignore `$ignoreId`), nama_lengkap, status_dalam_keluarga (`Rule::in(Siswa::STATUS_KELUARGA)`), anak_ke (integer, min:1, max:30), jumlah_saudara (integer, min:0, max:30), pernah_tk dan pernah_paud (required, boolean), hobi, cita_cita (max:100), no_hp (max:20), alamat, kabupaten_kota, kecamatan, kelurahan (max:100), kode_pos (digits:5), status_tinggal, jarak_ke_sekolah, transportasi, waktu_tempuh (masing-masing `Rule::in` konstanta model).
2. Koordinat kini opsional: `latitude` = `['nullable','numeric','between:-90,90','required_with:longitude']` dan `longitude` = `['nullable','numeric','between:-180,180','required_with:latitude']`.
3. `SiswaRequest::rules()`: `$id = $this->route('siswa')?->id; $rules = SiswaRules::dasar($id);` lalu blok khusus sekolah (kelas_id, user_id, orang_tua_id) tetap sama, `return $rules`.
4. Jalankan `php artisan test`: test lama harus tetap lulus.

### Tahap 4: Template Excel (Praktikum 22)

1. `App\Support\SiswaKolom`: konstanta `DAFTAR` (24 item, urutan persis di bawah; tiap item `[nama, wajib(bool), keterangan, contoh]`) dan `public static function kolom(): array` (= `array_column(self::DAFTAR, 0)`).

| No | Kolom | Wajib | Contoh |
|---|---|---|---|
| 1 | nik | ya | 6371000000000101 |
| 2 | nama_lengkap | ya | Budi Santoso |
| 3 | kelas | ya | X-A |
| 4 | status_dalam_keluarga | ya | Anak Kandung |
| 5 | anak_ke | ya | 1 |
| 6 | jumlah_saudara | ya | 2 |
| 7 | pernah_tk | ya | Ya |
| 8 | pernah_paud | ya | Tidak |
| 9 | hobi | ya | Membaca |
| 10 | cita_cita | ya | Guru |
| 11 | no_hp | ya | 081200000101 |
| 12 | alamat | ya | Jl. Contoh No. 5 RT 01 RW 02 |
| 13 | kabupaten_kota | ya | Kota Banjarmasin |
| 14 | kecamatan | ya | Banjarmasin Utara |
| 15 | kelurahan | ya | Sungai Miai |
| 16 | kode_pos | ya | 70122 |
| 17 | status_tinggal | ya | Tinggal Dengan Ayah dan Ibu |
| 18 | jarak_ke_sekolah | ya | Kurang dari 5 KM |
| 19 | transportasi | ya | Sepeda Motor |
| 20 | waktu_tempuh | ya | 10-19 menit |
| 21 | latitude | tidak | -3.2969 |
| 22 | longitude | tidak | 114.5953 |
| 23 | nama_orang_tua | tidak | Bapak Santoso |
| 24 | email_orang_tua | tidak | santoso@contoh.com |

   Seluruh nilai contoh **harus lolos** `SiswaRules::dasar()` (dipakai oleh tes).
2. `App\Exports\SiswaTemplateExport implements WithMultipleSheets` mengembalikan `[new DataSheet(), new PetunjukSheet()]`.
3. `App\Exports\Template\DataSheet` (`FromArray`, `WithHeadings`, `WithTitle` = "Data Siswa", `ShouldAutoSize`, `WithEvents`): `array()` kosong, `headings()` = `SiswaKolom::kolom()`. Pada event `AfterSheet` (sampai baris 1000):
   - Judul: tebal, huruf putih; latar `1F3864` untuk kolom wajib dan `7A869A` untuk opsional; `freezePane('A2')`.
   - Format **Teks** (`NumberFormat::FORMAT_TEXT`) untuk kolom nik, no_hp, kode_pos, latitude, longitude.
   - Dropdown (`DataValidation::TYPE_LIST`, `STYLE_STOP`, allow blank, pesan galat "Pilih salah satu nilai dari daftar.", `setFormula1('"a,b,c"')`, **jangan** memanggil `setShowDropDown(true)` karena itu menyembunyikan panah) untuk: kelas (dari tabel `kelas`), status_dalam_keluarga, pernah_tk dan pernah_paud (Ya,Tidak), status_tinggal, jarak_ke_sekolah, transportasi, waktu_tempuh (konstanta model). Lewati bila daftar kosong atau melebihi 255 karakter.
4. `PetunjukSheet` (`FromArray`, `WithHeadings` = Kolom, Wajib, Keterangan, Contoh isian; `WithTitle` = "Petunjuk"): baris dari `SiswaKolom::DAFTAR` ditambah blok "CATATAN PENTING" (8 butir: mulai baris 2 dan jangan ubah judul; jangan ubah format Teks; NIK ada=diperbarui, baru=ditambah; kelas harus terdaftar; email orang tua dan aturan nama orang tua; satu email boleh banyak baris; koordinat opsional dengan titik desimal; maksimal 1.000 baris dan 2 MB).
5. `SiswaImportController::template()` (`Gate::authorize('import', Siswa::class)`) mengembalikan `Excel::download(new SiswaTemplateExport(), 'template-import-siswa.xlsx')`.
6. `SiswaPolicy::import(User $user)` = `hasRole(Role::Sekolah)`.

### Tahap 5: Logika import (Praktikum 23)

Buat `App\Imports\SiswaImport implements ToCollection, WithHeadingRow`. Konstruktor `__construct(private bool $periksaSaja = false)`. Properti publik `array $hasil` berisi `dry_run, total, dibuat, diperbarui, gagal, galat (list [baris, nama, pesan[]]), akun_baru (list [nama, email, password])`. Perilaku wajib:

1. Koleksi kosong: galat "Berkas tidak berisi data siswa."; bila kolom template ada yang hilang (`array_diff(SiswaKolom::kolom(), array_keys($rows->first()->toArray()))`): galat "Kolom tidak ditemukan: ... Gunakan template terbaru dan jangan mengubah baris judul." lalu berhenti.
2. Buang baris kosong; **nomor baris Excel = indeks + 2**. Isi `total`. Bila tidak ada baris: galat; bila lebih dari `MAKS_BARIS = 1000`: isi `gagal`, galat, berhenti.
3. Peta kelas: `Kelas::pluck('id','nama')` dengan kunci `Str::lower(trim($nama))`.
4. Seluruh pemrosesan dalam **satu `DB::transaction`**, memanggil `prosesBaris($nomor, $data)` per baris.
5. **Normalisasi** per baris (semua kolom dari `SiswaKolom::kolom()`): trim string, `''` menjadi `null`; NIK non-string (angka dari Excel) menghasilkan pesan "NIK terbaca sebagai angka sehingga digit terakhirnya bisa berubah. Ubah format kolom menjadi Teks, lalu ketik ulang NIK." (nilai diubah ke string dengan `sprintf('%.0f')`); `kode_pos`, `no_hp`, `kelas` bertipe angka diubah ke string; `pernah_tk` dan `pernah_paud`: `ya|y|1|true` menjadi 1, `tidak|t|0|false` menjadi 0, selain itu dibiarkan agar ditolak aturan boolean; `latitude` dan `longitude`: koma diganti titik; `email_orang_tua` huruf kecil.
6. Pemeriksaan baris:
   - NIK ganda di berkas: "NIK sama dengan baris N." (catat NIK pertama kali muncul).
   - `$siswa = Siswa::where('nik', $nik)->first()` (ada = perbarui, tidak ada = buat).
   - `Validator::make($data, SiswaRules::dasar($siswa?->id) + ['kelas'=>['required','string'], 'nama_orang_tua'=>['nullable','string','max:255'], 'email_orang_tua'=>['nullable','email','max:255']], PESAN)`. `PESAN` adalah konstanta pesan Bahasa Indonesia berkunci nama aturan (required, required_with, digits, integer, numeric, between.numeric, min.numeric, max.numeric, max.string, in, email, boolean, unique, string) memakai `:attribute`.
   - Kelas tidak ada: `Kelas "X" tidak ditemukan. Buat dulu pada menu Kelas.`
   - Orang tua (`periksaOrtu`): bila email terisi dan akun ada dengan peran selain `OrangTua`: "Email X sudah dipakai akun dengan peran {label}."; bila akun belum ada, belum direncanakan pada mode periksa saja, dan `nama_orang_tua` kosong: "Nama orang tua wajib diisi karena email X belum terdaftar."
   - Bila ada pesan: `gagal++`, catat galat, **lewati baris** (baris lain tetap diproses).
7. Penyimpanan baris yang valid:
   - Atribut = `Arr::only($data, array_keys(SiswaRules::dasar())) + ['kelas_id' => $kelasId]`. Bila `latitude` null, `unset` latitude dan longitude (**tidak menimpa** koordinat lama dengan kosong).
   - Mode `periksaSaja`: **tidak menulis apa pun**; hanya `rencanakanOrtu` (catat akun yang akan dibuat dengan `password => null`, tanpa duplikat per email).
   - Mode sungguhan: `ambilAtauBuatOrtu` (email kosong menghasilkan null; akun ada menghasilkan id; belum ada: buat `new User(['name','email','password'])` dengan password acak `Str::password(10, symbols: false)`, set `role = Role::OrangTua`, simpan, catat ke `akun_baru` beserta password) lalu isi `orang_tua_id` bila ada; simpan dengan `Siswa::updateOrCreate(['nik' => $nik], $atribut)`.
   - Hitung `dibuat` atau `diperbarui`.

### Tahap 6: Halaman import (Praktikum 24)

1. `SiswaImportController`: `create()` (view `siswa.import` dengan `hasil` null); `store(Request)`: `Gate::authorize('import', ...)`, validasi `file` = `['required','file','extensions:xlsx','max:2048']` dengan pesan Indonesia, `new SiswaImport($request->boolean('periksa_saja'))`, `Excel::import(...)` dibungkus `try/catch (\Throwable)` (`report($e)`, lalu `back()->withErrors(['file' => 'Berkas tidak dapat dibaca. Pastikan berkas adalah Excel (.xlsx) yang valid.'])`), dan **mengembalikan view langsung** (bukan redirect) dengan `$import->hasil`, supaya password akun baru tidak masuk sesi.
2. View `siswa/import.blade.php`: dua kartu (unduh template; form unggah `multipart/form-data` dengan input `file` `.xlsx`, centang `periksa_saja` **aktif secara bawaan**, tombol Proses); bila ada hasil: kartu ringkasan (Total baris, Dibuat atau Akan dibuat, Diperbarui atau Akan diperbarui, Gagal; biru untuk periksa saja, hijau untuk sungguhan), tabel "Baris yang bermasalah" (Baris, Nama, daftar pesan), tabel akun orang tua baru (Nama, Email, Password; password null ditampilkan "dibuat saat import sungguhan"; peringatan bahwa password hanya tampil sekali bila bukan mode periksa saja), dan tautan ke daftar siswa. Semua nilai memakai `{{ }}`.
3. Route di dalam grup `auth` dan `role:sekolah`: `GET /siswa/import` (`siswa.import.create`), `POST /siswa/import` (`siswa.import.store`), `GET /siswa/import/template` (`siswa.import.template`).
4. Layout: menu **Import Siswa** (route `siswa.import.create`) untuk `Role::Sekolah`, di bawah komentar `MENU-ROLE`.

### Tahap 7: Ekspor PDF daftar (Praktikum 25)

1. Scope pada `Siswa`: `scopeCari(Builder $query, ?string $q)` (nama_lengkap, nik, atau kecamatan LIKE, dibungkus `where(function)` di dalam `when`) dan `scopeDiKelas(Builder $query, $kelasId)` (`when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId))`).
2. `SiswaController::index`: `Siswa::query()->dapatDilihat($user)->with('kelas')->cari($q)->diKelas($kelasId)->orderBy('nama_lengkap')->paginate(10)->withQueryString()`; kirim `siswas, q, kelasId, pilihanKelas`. Private `pilihanKelas(User $user): Collection`: sekolah = semua kelas (`pluck('nama','id')`), wali kelas = `$user->kelasDiampu()` saja, lainnya `collect()`.
3. `SiswaPolicy::export(User $user)` = `hasRole(Role::Sekolah, Role::WaliKelas)`.
4. `SiswaExportController::daftar(Request)`: `Gate::authorize('export', Siswa::class)`; query = `dapatDilihat + cari + diKelas`; bila `count() > 500` kembalikan `back()->with('error', 'Data terlalu banyak untuk satu PDF (maksimal 500 siswa). Gunakan filter kelas.')`; ambil data dengan `with(['kelas','orangTua'])->orderBy('nama_lengkap')`; `Pdf::loadView('siswa.pdf.daftar', [...siswas, filterKelas, filterCari, pencetak (nama), waktuCetak])->setPaper('a4','landscape')->download('data-siswa-'.now()->format('Ymd-His').'.pdf')`. `waktuCetak()` = `now()->timezone('Asia/Makassar')->locale('id')->translatedFormat('d F Y, H:i').' WITA'`.
5. View `siswa/pdf/daftar.blade.php` (**hanya tabel dan CSS sederhana, tanpa flexbox atau grid**; font `DejaVu Sans`; `<meta charset="UTF-8">`; `thead { display: table-header-group; }` dan `tr { page-break-inside: avoid; }`): judul "DAFTAR DATA SISWA", baris filter (kelas, pencarian) dan jumlah siswa, tabel kolom No, NIK, Nama Lengkap, Kelas, Orang Tua, Alamat, Kelurahan/Kecamatan, Jarak, Transportasi, Koordinat (`-` bila kosong), zebra, dan footer `position: fixed` berisi "DOKUMEN RAHASIA - berisi data pribadi siswa. Dicetak oleh X, waktu." serta nomor halaman memakai CSS `content: "Halaman " counter(page)`. **Jangan** memakai `<script type="text/php">` dan jangan mengaktifkan `isPhpEnabled`.
6. Route di dalam grup `auth`: `GET /ekspor/siswa/pdf` (`siswa.ekspor.daftar`).
7. Ganti `siswa/index.blade.php`: tombol **Import Excel** (`@can('import', Siswa::class)`), **Ekspor PDF** (`@can('export', Siswa::class)`, link `route('siswa.ekspor.daftar', request()->only('q','kelas'))`), **+ Tambah Siswa** (`@can('create', ...)`), form filter (select kelas bila `$pilihanKelas->isNotEmpty()`, input `q`, tombol Cari), tabel dengan kolom No, Nama, NIK, Kelas, Kecamatan, Koordinat (lencana "Belum ada titik" bila kosong), Aksi (Ubah dan Hapus dengan `@can`), dan paginasi.

### Tahap 8: Biodata PDF (Praktikum 26)

1. `SiswaExportController::biodata(Request, Siswa $siswa)`: `Gate::authorize('view', $siswa)`; `$siswa->load(['kelas.waliKelas','orangTua'])`; `Pdf::loadView('siswa.pdf.biodata', [...])->setPaper('a4','portrait')->stream('biodata-'.Str::slug($siswa->nama_lengkap).'.pdf')`.
2. View `siswa/pdf/biodata.blade.php`: judul "BIODATA SISWA", bagian bertabel label dan nilai: Data Pribadi (NIK, nama, status keluarga, anak ke dan jumlah saudara, pernah TK dan PAUD sebagai Ya atau Tidak, hobi, cita-cita, no HP), Kelas dan Orang Tua (kelas, wali kelas, nama dan email orang tua, `-` bila kosong), Alamat Tinggal, Jarak dan Transportasi, Titik Koordinat Rumah (koordinat atau "Belum ditentukan", dan tautan `https://www.openstreetmap.org/?mlat=..&mlon=..#map=17/lat/lon` dengan `&amp;` hanya bila ada koordinat). Footer sama seperti daftar. Tanpa gambar dari internet (`isRemoteEnabled` mati).
3. Route di dalam grup `auth`: `GET /siswa/{siswa}/pdf` (`siswa.ekspor.biodata`).
4. Pada kolom Aksi `siswa/index.blade.php`, tambah tautan **PDF** (`target="_blank"`) dibungkus `@can('view', $siswa)`, sebelum tombol Ubah.

### Tahap 9: Tes (Praktikum 27)

1. Pastikan `phpunit.xml` memakai `DB_CONNECTION=sqlite` dan `DB_DATABASE=:memory:` (tidak dikomentari).
2. Tes lama (`SiswaTest`, `RbacTest`) harus tetap lulus. Perbaiki kode aplikasi, bukan melemahkan tes. Jika tes lama bergantung pada koordinat wajib, sesuaikan dengan aturan baru (koordinat opsional, setengah terisi ditolak).
3. Buat `tests/Feature/ImportExportTest.php` (PHPUnit, `RefreshDatabase`). Helper: `akun(Role, ?email)` (pakai `forceFill`); `baris(array $ubah)` = `array_combine(SiswaKolom::kolom(), array_column(SiswaKolom::DAFTAR, 3))` digabung `$ubah`; `xlsx(array $baris): UploadedFile` membuat berkas **xlsx asli** dengan `PhpOffice\PhpSpreadsheet` (judul pada baris 1, setiap nilai ditulis `setCellValueExplicit([$kolom, $baris], (string) $nilai, DataType::TYPE_STRING)`, simpan ke file sementara `.xlsx`, `new UploadedFile($path, 'siswa.xlsx', null, null, true)`); `siswa(array $ubah)` (buat langsung ke database dengan NIK unik); `impor(User, array $baris, bool $periksaSaja)` (POST ke `siswa.import.store`). Skenario wajib:

| Test | Yang diperiksa |
|---|---|
| hak akses import | wali kelas: `siswa.import.create`, `siswa.import.template`, dan POST import semuanya 403; sekolah membuka halaman import 200 |
| template | sekolah: `assertDownload('template-import-siswa.xlsx')` |
| import sukses | kelas X-A ada; dua baris dengan email orang tua sama (baris kedua tanpa `nama_orang_tua`); hasil memuat "Import selesai"; 2 siswa, `kelas_id` benar, **hanya 1** akun peran orang tua, kedua siswa menunjuk akun itu |
| periksa saja | halaman memuat "belum ada data yang disimpan"; tabel siswa dan akun orang tua tetap kosong |
| baris salah | baris valid + baris kelas "Z-9" + baris NIK kembar: tampil "tidak ditemukan" dan "NIK sama dengan baris 2"; hanya 1 siswa tersimpan |
| import ulang | dua kali import (hobi diubah): tetap 1 siswa, hobi terbarui, 1 akun orang tua |
| email peran lain | email contoh dipakai akun wali kelas: tampil "sudah dipakai akun dengan peran Wali Kelas"; tidak ada siswa tersimpan |
| koordinat | latitude dan longitude kosong: siswa dibuat dengan koordinat null; setelah koordinat diisi manual lalu import ulang dengan kosong: koordinat lama **tidak berubah** |
| ekspor daftar | sekolah dan wali kelas: 200 dengan header `content-type: application/pdf`; orang tua dan siswa: 403 |
| biodata | orang tua: anaknya 200 PDF, siswa lain 403; admin 403 |

### Tahap 10: Verifikasi (wajib, jangan dilewati)

1. `php artisan migrate:fresh --seed` berhasil (pada MySQL jika tersedia).
2. `php artisan route:list --except-vendor` memuat `siswa.import.create`, `siswa.import.store`, `siswa.import.template`, `siswa.ekspor.daftar`, `siswa.ekspor.biodata`.
3. `npm run build` sukses.
4. `php artisan test` **semua lulus** (tes lama dan baru).
5. Jalankan `php artisan serve` di latar belakang. Dengan `curl` memakai cookie jar (ambil token CSRF dari `/login`, lalu `POST /login`) uji:
   - Login `sekolah@sekolah.test`: `GET /siswa/import/template` menghasilkan berkas xlsx (jenis `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`; pastikan berkas valid zip dengan `unzip -l`). `GET /ekspor/siswa/pdf` menghasilkan berkas yang diawali `%PDF`.
   - Login `walikelas1@sekolah.test`: `GET /siswa/import` 403; `GET /ekspor/siswa/pdf` 200 PDF.
   - Login `ortu1@sekolah.test`: `GET /ekspor/siswa/pdf` 403; biodata anak sendiri 200 PDF; biodata siswa lain 403.
   Hentikan server setelah selesai.
6. Jika verifikasi memakai SQLite karena MySQL tidak tersedia, katakan dengan jelas pada laporan.

## 5. Batasan (jangan dilakukan)

- Jangan memasang paket selain `maatwebsite/excel` dan `barryvdh/laravel-dompdf`.
- Jangan memakai flexbox, grid, variabel CSS, atau `<script type="text/php">` pada view PDF; jangan mengaktifkan `isPhpEnabled` atau `isRemoteEnabled`.
- Jangan menyimpan berkas unggahan import secara permanen.
- Jangan mengembalikan hasil import lewat redirect atau session flash (password akun baru tidak boleh tersimpan di sesi).
- Jangan membuat import menimpa koordinat lama dengan nilai kosong, dan jangan membuat kelas baru otomatis dari nama kelas yang tidak dikenal.
- Jangan membuat orang tua atau siswa dapat mengekspor daftar, dan jangan membuat admin dapat melihat data siswa.
- Jangan mengubah nama kolom, route, atau konstanta yang sudah ada.

## 6. Kriteria Selesai

- [ ] Template Excel terunduh dengan 2 sheet, 24 kolom, format Teks pada kolom NIK, dan dropdown berfungsi.
- [ ] Import membuat dan memperbarui siswa berdasarkan NIK, menempatkan ke kelas, serta membuat atau menautkan akun orang tua (satu akun untuk banyak anak).
- [ ] Baris salah dilaporkan dengan nomor baris dan alasan, baris benar tetap tersimpan; mode "Periksa saja" tidak menyimpan apa pun.
- [ ] Koordinat opsional: siswa tanpa titik tampil dengan lencana pada daftar dan tidak muncul di peta.
- [ ] PDF daftar mengikuti filter dan hak akses; biodata PDF hanya untuk yang berhak.
- [ ] `php artisan test` lulus semua dan `npm run build` sukses.

## 7. Laporan Akhir

Tampilkan ringkasan singkat: file baru dan file yang diubah, hasil `php artisan test`, hasil uji `curl` per akun (status HTTP dan jenis berkas), database yang dipakai saat verifikasi (MySQL atau SQLite), catatan ekstensi atau memori yang disesuaikan, serta perintah menjalankan aplikasi:

```
php artisan migrate:fresh --seed
php artisan serve
npm run dev
```

Alur uji manual: login `sekolah@sekolah.test` (password `password`), buka menu **Import Siswa**, unduh template, isi data dummy, unggah dengan centang "Periksa saja", lalu unggah ulang tanpa centang.
