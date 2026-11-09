/* ==========================================================
   Situs Profil Interaktif - Pertemuan 4 (JavaScript dasar dan DOM)
   Berkas ini dimuat di semua halaman, sehingga setiap bagian
   memeriksa dulu apakah elemennya ada di halaman yang sedang dibuka.
   ========================================================== */

/* ---------- Praktikum 1: uji coba tautan ---------- */
console.log("JavaScript aktif di", document.title);

/* ---------- Praktikum 2: fungsi dan percabangan (kalkulator nilai) ---------- */
function hurufMutu(nilai) {
  if (nilai >= 85) return "A";
  else if (nilai >= 75) return "B";
  else if (nilai >= 65) return "C";
  else if (nilai >= 50) return "D";
  return "E";
}

// Rata-rata dari sebuah array angka, memakai perulangan for...of
function rataRata(daftar) {
  let total = 0;
  for (const angka of daftar) {
    total += angka;
  }
  return total / daftar.length;
}

const formNilai = document.querySelector("#form-nilai");

if (formNilai) {
  formNilai.addEventListener("submit", (e) => {
    e.preventDefault();
    // Nilai dari input selalu string, ubah ke angka dengan Number()
    const tugas = Number(document.querySelector("#tugas").value);
    const uts = Number(document.querySelector("#uts").value);
    const uas = Number(document.querySelector("#uas").value);

    const akhir = tugas * 0.3 + uts * 0.3 + uas * 0.4;
    const status = akhir >= 50 ? "Lulus" : "Tidak lulus";

    document.querySelector("#hasil").textContent =
      `Nilai akhir: ${akhir.toFixed(1)} (${hurufMutu(akhir)}) \u2014 ${status}`;
  });
}

/* ---------- Praktikum 3: mode gelap ---------- */
const tombolTema = document.querySelector("#tombol-tema");

if (tombolTema) {
  tombolTema.addEventListener("click", () => {
    document.body.classList.toggle("gelap");
    const gelap = document.body.classList.contains("gelap");
    tombolTema.textContent = gelap ? "Mode terang" : "Mode gelap";
  });
}

/* ---------- Praktikum 3: sapaan sesuai waktu ---------- */
const salam = document.querySelector("#salam");

if (salam) {
  const jam = new Date().getHours();
  let sapaan;
  if (jam < 11) sapaan = "Selamat pagi";
  else if (jam < 15) sapaan = "Selamat siang";
  else if (jam < 18) sapaan = "Selamat sore";
  else sapaan = "Selamat malam";
  salam.textContent = `${sapaan}, selamat datang di situs saya!`;
}

/* ---------- Praktikum 4: daftar minat dinamis ---------- */
const daftarMinat = document.querySelector("#daftar-minat");
const formMinat = document.querySelector("#form-minat");

if (formMinat) {
  formMinat.addEventListener("submit", (e) => {
    e.preventDefault();
    const input = document.querySelector("#minat-baru");
    const galat = document.querySelector("#galat-minat");
    const teks = input.value.trim();

    if (teks === "") {
      galat.textContent = "Minat tidak boleh kosong.";
      return;
    }
    galat.textContent = "";

    const li = document.createElement("li");
    li.textContent = teks;   // textContent: teks dari pengguna diperlakukan sebagai teks biasa
    const hapus = document.createElement("button");
    hapus.textContent = "Hapus";
    hapus.className = "hapus";
    hapus.type = "button";
    li.append(hapus);
    daftarMinat.append(li);
    input.value = "";
    input.focus();
  });

  // event delegation: satu listener untuk semua tombol Hapus,
  // termasuk yang dibuat belakangan
  daftarMinat.addEventListener("click", (e) => {
    if (e.target.matches(".hapus")) {
      e.target.closest("li").remove();
    }
  });
}

/* ---------- Praktikum 5: validasi form kontak ---------- */
const formKontak = document.querySelector("form[action='terima.php']");

function tampilGalat(id, pesan) {
  document.querySelector("#galat-" + id).textContent = pesan;
}

if (formKontak) {
  formKontak.addEventListener("submit", (e) => {
    const nama = document.querySelector("#nama").value.trim();
    const email = document.querySelector("#email").value.trim();
    const pesan = document.querySelector("#pesan").value.trim();
    const setuju = document.querySelector("input[name='setuju']").checked;
    let valid = true;

    tampilGalat("nama", "");
    tampilGalat("email", "");
    tampilGalat("pesan", "");
    tampilGalat("setuju", "");

    if (nama.length < 3) {
      tampilGalat("nama", "Nama minimal 3 karakter.");
      valid = false;
    }
    if (!email.includes("@") || !email.includes(".")) {
      tampilGalat("email", "Format surel tidak valid.");
      valid = false;
    }
    if (pesan.length < 10) {
      tampilGalat("pesan", "Pesan minimal 10 karakter.");
      valid = false;
    }
    // novalidate juga menonaktifkan "required" pada checkbox,
    // jadi persetujuan diperiksa di sini
    if (!setuju) {
      tampilGalat("setuju", "Persetujuan wajib dicentang.");
      valid = false;
    }

    if (!valid) e.preventDefault();   // batalkan pengiriman bila ada yang salah
  });

  // penghitung karakter secara langsung
  const kolomPesan = document.querySelector("#pesan");
  const hitung = document.querySelector("#hitung");
  kolomPesan.addEventListener("input", () => {
    hitung.textContent = `${kolomPesan.value.length} / 200`;
  });
}

/* ---------- Praktikum 6: debugging ---------- */
// Versi awal (salah):  return a + b + c / 3;
// Penyebab: pembagian dikerjakan lebih dulu daripada penjumlahan,
//           sehingga hasilnya a + b + (c / 3). Perbaikan: beri tanda kurung.
function rataRataTiga(a, b, c) {
  return (a + b + c) / 3;
}
