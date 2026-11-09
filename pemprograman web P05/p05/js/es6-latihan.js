// Latihan fitur ES6. Dimuat sebagai modul: <script type="module" src="js/es6-latihan.js">
import ringkas, { kapital, hitungRata, formatRupiah } from "./util.js";

const keluaran = document.querySelector("#keluaran");
const baris = [];
const tulis = (judul, nilai) =>
  baris.push(`${judul}: ${typeof nilai === "string" ? nilai : JSON.stringify(nilai)}`);

// 1. Template literal
const nama = "Ani";
const sks = 3;
tulis("1. Template literal", `Halo ${nama}, kamu mengambil ${sks} SKS`);

// 2. Arrow function
const kuadrat = (x) => x * x;
tulis("2. Arrow function", kuadrat(7));

// 3. Parameter bawaan (default parameter)
const sapa = (siapa = "Tamu") => `Halo, ${siapa}!`;
tulis("3. Parameter bawaan", [sapa(), sapa("Budi")]);

// 4. Destructuring object (termasuk ganti nama dan objek bersarang)
const mahasiswa = { nama: "Ani Lestari", nim: "2026010001", alamat: { kota: "Banjarmasin" } };
const { nama: namaLengkap, nim, alamat: { kota } } = mahasiswa;
tulis("4. Destructuring object", `${namaLengkap} / ${nim} / ${kota}`);

// 5. Destructuring array dan rest
const [pertama, kedua, ...sisa] = ["HTML", "CSS", "JavaScript", "PHP"];
tulis("5. Destructuring array", { pertama, kedua, sisa });

// 6. Spread: menggabung dan menyalin tanpa mengubah aslinya
const frontend = ["HTML", "CSS"];
const semua = [...frontend, "JavaScript"];
const profil = { nama: "Ani", prodi: "SI" };
const profilBaru = { ...profil, prodi: "Sistem Informasi", aktif: true };
tulis("6. Spread array", semua);
tulis("6. Spread object", profilBaru);
tulis("6. Objek asli tidak berubah", profil);

// 7. Rest parameter (fungsi dari modul)
tulis("7. Rest parameter", hitungRata(80, 90, 70, 100));

// 8. Optional chaining (?.) dan nullish coalescing (??)
const tanpaAlamat = { nama: "Budi" };
tulis("8. ?. dan ??", `${tanpaAlamat.alamat?.kota ?? "Kota belum diisi"} | ${mahasiswa.alamat?.kota ?? "-"}`);

// 9. Metode array: filter, map, reduce, find, some, every, sort
const daftarNilai = [
  { nama: "Ani", nilai: 90 },
  { nama: "Budi", nilai: 72 },
  { nama: "Citra", nilai: 85 },
  { nama: "Dedi", nilai: 58 },
];
tulis("9. filter (nilai >= 75)", daftarNilai.filter((m) => m.nilai >= 75).map((m) => m.nama));
tulis("9. map (nama saja)", daftarNilai.map((m) => m.nama));
tulis("9. reduce (total)", daftarNilai.reduce((jumlah, m) => jumlah + m.nilai, 0));
tulis("9. find (nilai < 60)", daftarNilai.find((m) => m.nilai < 60));
tulis("9. some / every (>= 50)", [daftarNilai.some((m) => m.nilai < 60), daftarNilai.every((m) => m.nilai >= 50)]);
tulis("9. sort menurun", [...daftarNilai].sort((a, b) => b.nilai - a.nilai).map((m) => m.nama));

// 10. Fungsi dari modul util.js
tulis("10. Modul: kapital", kapital("pemrograman web"));
tulis("10. Modul: formatRupiah", formatRupiah(1500000));
tulis("10. Modul: ringkas (default export)", ringkas("JavaScript modern membuat kode lebih ringkas dan mudah dibaca", 30));

keluaran.textContent = baris.join("\n");
