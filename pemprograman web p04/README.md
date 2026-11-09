# Situs Profil Interaktif dengan JavaScript (Pertemuan 4)

Hasil pengerjaan **Modul Praktikum Pertemuan 4: Membuat Situs Profil Interaktif dengan JavaScript**, mata kuliah Pemrograman Web.
Dasarnya adalah situs hasil Pertemuan 3 (folder `p03`). Data pada situs adalah contoh; ganti dengan data Anda.

## Struktur

```
p04/
├── index.html      Beranda: sapaan sesuai waktu, mode gelap, daftar minat dinamis
├── jadwal.html     Tabel jadwal (mendukung mode gelap)
├── nilai.html      Kalkulator nilai akhir (halaman baru)
├── kontak.html     Form kontak dengan validasi JavaScript dan penghitung karakter
├── tailwind.html   Kartu profil Tailwind dari Pertemuan 3 (tanpa JavaScript situs)
├── css/style.css   Gaya Pertemuan 3 + gaya mode gelap, pesan galat, tombol hapus
├── js/app.js       Seluruh JavaScript, disusun per praktikum
├── img/foto.jpg    Foto placeholder
└── README.md
```

## Pemetaan ke praktikum

| Praktikum | Hasil |
|---|---|
| 1 Tautan JS dan Console | `<script src="js/app.js" defer></script>` di empat halaman; `console.log` uji coba |
| 2 Fungsi dan percabangan | `hurufMutu()`, `rataRata()` (for...of), penanganan submit `#form-nilai`, `Number()` untuk input |
| 3 DOM: mode gelap dan sapaan | `classList.toggle("gelap")` pada `#tombol-tema`; `#salam` berdasarkan `getHours()` |
| 4 Daftar minat dinamis | `createElement`, `append`, `textContent`, event delegation untuk tombol Hapus |
| 5 Validasi form kontak | `novalidate`, pesan galat per isian, `preventDefault()`, penghitung karakter (event `input`) |
| 6 Debugging | `rataRataTiga()` sudah diperbaiki; penyebab bug dicatat sebagai komentar |
| 7 Git | lihat di bawah |

## Catatan

- Karena `novalidate` juga menonaktifkan `required` pada checkbox, persetujuan diperiksa di JavaScript (`galat-setuju`).
- Mode gelap sengaja tidak bertahan saat pindah halaman (pertanyaan analisis modul). Tantangan opsionalnya memakai `localStorage`.
- Form kontak masih mengirim ke `terima.php` yang sengaja belum dibuat.
- Berkas `tailwind.html` butuh koneksi internet (CDN Tailwind).

## Pemeriksaan yang sudah dilakukan

- 45 uji otomatis pada `js/app.js` di lingkungan DOM tiruan (jsdom) lulus: tidak ada error JS di keempat halaman; mode gelap; sapaan; kalkulator termasuk batas nilai 85/75/65/50; tambah, hapus, dan penolakan input kosong pada daftar minat; teks HTML tidak diinterpretasi; validasi form (kosong, salah, benar, tanpa persetujuan); penghitung karakter; `rataRata` dan `rataRataTiga`.
- Parser HTML5 tidak melaporkan kesalahan; tidak ada id ganda; semua tautan, gambar, CSS, dan skrip valid; CSS dan JavaScript lolos pemeriksaan sintaks.
- Tampilan visual, mode gelap secara visual, dan perilaku di browser sungguhan **belum** diuji; uji dengan Live Server dan DevTools.

## Git

```
git init
git add .
git commit -m "Situs profil interaktif dengan JavaScript"
git remote add origin https://github.com/USERNAME/pemweb-pertemuan-04.git
git branch -M main
git push -u origin main
```
