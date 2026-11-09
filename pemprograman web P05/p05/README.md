# Data dan Interaksi: ES6, JSON, Fetch API, Alpine.js (Pertemuan 5)

Hasil pengerjaan **Modul Praktikum Pertemuan 5: Data dan Interaksi**, mata kuliah Pemrograman Web.
Dasarnya adalah situs hasil Pertemuan 4 (folder `p04`). Data pada situs adalah contoh; ganti dengan data Anda.

## Penting: jalankan lewat server

Halaman ini memakai `fetch` dan modul JavaScript, yang **tidak berfungsi** bila dibuka lewat `file:///`.
Buka dengan **Live Server** (VS Code) atau lewat `http://localhost/...` (Laragon).

## Struktur

```
p05/
├── index.html, jadwal.html, nilai.html, kontak.html   Halaman lama, navigasi diperbarui (8 menu)
├── es6.html        Latihan ES6; skrip bertipe module
├── proyek.html     Daftar proyek dari data/proyek.json (fetch, filter, pencarian)
├── pengguna.html   GET dan POST ke API publik JSONPlaceholder
├── alpine.html     Empat demo Alpine.js
├── tailwind.html   Kartu profil Tailwind dari Pertemuan 3
├── data/
│   └── proyek.json     Enam proyek contoh
├── js/
│   ├── app.js          Dari Pertemuan 4 (mode gelap, nilai, minat, validasi kontak)
│   ├── util.js         Modul: named export dan default export
│   ├── es6-latihan.js  Sepuluh latihan fitur ES6, mengimpor util.js
│   ├── proyek.js       fetch lokal, async/await, filter, pencarian, tiga keadaan
│   ├── pengguna.js     fetch GET dan POST, ?. dan ??
│   └── alpine-demo.js  Komponen Alpine.data()
├── css/style.css   Gaya Pertemuan 4 + tombol filter, status, tag, keluaran, x-cloak
├── img/foto.jpg    Foto placeholder
└── README.md
```

## Pemetaan ke praktikum

| Praktikum | Hasil |
|---|---|
| 1 Proyek dan navigasi | Menu 8 tautan di semua halaman, `aria-current` per halaman |
| 2 ES6 dan modul | `util.js`, `es6-latihan.js`: template literal, arrow, parameter bawaan, destructuring, spread, rest, `?.`, `??`, `map/filter/reduce/find/some/every/sort`, `import/export` |
| 3 JSON | `data/proyek.json` (6 proyek, 4 kategori) |
| 4 Fetch lokal | `proyek.html` + `proyek.js`: tombol filter dari `new Set`, pencarian, status memuat, berhasil, dan gagal |
| 5 GET API publik | `pengguna.html` + `pengguna.js`: tabel pengguna, pencarian, `address?.city ?? "-"` |
| 6 POST | Form komentar: validasi, `fetch` POST dengan header JSON, status 201 dan ID baru, balasan JSON |
| 7 Alpine.js | Penghitung, tab, daftar tugas (`Alpine.data`), data API dengan `x-init` dan getter |
| 8 Git | lihat di bawah |

## Catatan

- `alpine-demo.js` harus dimuat **sebelum** pustaka Alpine; keduanya memakai `defer`.
- Skrip klasik berbagi lingkup global: `pengguna.js` memakai `tampilGalat()` dari `app.js`, dan nama variabel antarskrip tidak boleh sama.
- Data dari API ditampilkan dengan `textContent` (bukan `innerHTML`) agar aman dari XSS.
- JSONPlaceholder hanya menyediakan data contoh; data yang di-POST tidak benar-benar tersimpan.
- Pustaka Alpine dimuat dari CDN (`alpinejs@3.x.x`), jadi halaman Alpine butuh koneksi internet.

## Pemeriksaan yang sudah dilakukan

- 77 uji otomatis lulus di lingkungan DOM tiruan (jsdom) dengan `fetch` tiruan: tidak ada error JS di enam halaman; navigasi aktif benar; Proyek (6 kartu, filter, pencarian, kombinasi filter dan cari, reset, galat 404, galat jaringan); Pengguna (tabel, `?.`/`??`, pencarian, validasi POST, isi permintaan POST, status 201, galat jaringan); sepuluh keluaran latihan ES6; ekspor modul `util.js`; keempat demo Alpine (memakai paket Alpine dari npm).
- Parser HTML5 tidak melaporkan kesalahan; tidak ada id ganda; semua tautan, gambar, CSS, dan skrip valid; CSS lolos pengurai; `proyek.json` adalah JSON valid.
- **Belum** diuji di browser sungguhan: tampilan visual, pemuatan modul `type="module"`, akses ke API JSONPlaceholder yang sebenarnya, dan Alpine dari CDN. Uji dengan Live Server dan DevTools.

## Git

```
git init
git add .
git commit -m "ES6, JSON, Fetch API, dan Alpine.js"
git remote add origin https://github.com/USERNAME/pemweb-pertemuan-05.git
git branch -M main
git push -u origin main
```
