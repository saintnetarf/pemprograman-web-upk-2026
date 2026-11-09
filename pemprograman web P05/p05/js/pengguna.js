// Halaman Pengguna: GET dan POST ke API publik JSONPlaceholder (data contoh, bukan data sungguhan)
const URL_API = "https://jsonplaceholder.typicode.com";

const isiPengguna = document.querySelector("#isi-pengguna");
const statusPengguna = document.querySelector("#status-pengguna");
const kolomCariPengguna = document.querySelector("#cari-pengguna");
let semuaPengguna = [];

function tampilkanPengguna() {
  const kata = kolomCariPengguna.value.trim().toLowerCase();
  const hasil = semuaPengguna.filter(({ name, email }) =>
    `${name} ${email}`.toLowerCase().includes(kata)
  );

  const baris = hasil.map(({ name, email, address, company }) => {
    const tr = document.createElement("tr");
    // ?. dan ?? menjaga agar data yang tidak lengkap tidak menyebabkan error
    for (const teks of [name, email, address?.city ?? "-", company?.name ?? "-"]) {
      const td = document.createElement("td");
      td.textContent = teks;
      tr.append(td);
    }
    return tr;
  });

  isiPengguna.replaceChildren(...baris);
  statusPengguna.className = "status";
  statusPengguna.textContent = `${hasil.length} dari ${semuaPengguna.length} pengguna.`;
}

async function muatPengguna() {
  statusPengguna.className = "status";
  statusPengguna.textContent = "Memuat data pengguna...";
  try {
    const respons = await fetch(`${URL_API}/users`);
    if (!respons.ok) throw new Error(`Status ${respons.status}`);
    semuaPengguna = await respons.json();
    tampilkanPengguna();
  } catch (galat) {
    statusPengguna.className = "status error";
    statusPengguna.textContent = `Gagal memuat data: ${galat.message}`;
  }
}

// POST: mengirim data JSON ke server
const formKomentar = document.querySelector("#form-komentar");

async function kirimKomentar(e) {
  e.preventDefault();
  const judul = document.querySelector("#judul-komentar").value.trim();
  const isi = document.querySelector("#isi-komentar").value.trim();
  const hasilKirim = document.querySelector("#hasil-kirim");
  const balasan = document.querySelector("#balasan");

  // tampilGalat() berasal dari app.js (skrip klasik berbagi lingkup global)
  tampilGalat("judul", "");
  tampilGalat("isi", "");
  let valid = true;
  if (judul.length < 3) {
    tampilGalat("judul", "Judul minimal 3 karakter.");
    valid = false;
  }
  if (isi.length < 10) {
    tampilGalat("isi", "Isi minimal 10 karakter.");
    valid = false;
  }
  if (!valid) return;

  hasilKirim.className = "status";
  hasilKirim.textContent = "Mengirim...";
  try {
    const respons = await fetch(`${URL_API}/posts`, {
      method: "POST",
      headers: { "Content-Type": "application/json; charset=UTF-8" },
      body: JSON.stringify({ title: judul, body: isi, userId: 1 }),
    });
    if (!respons.ok) throw new Error(`Status ${respons.status}`);
    const data = await respons.json();
    hasilKirim.textContent = `Berhasil dikirim (status ${respons.status}). ID baru: ${data.id}`;
    balasan.textContent = JSON.stringify(data, null, 2);
    formKomentar.reset();
  } catch (galat) {
    hasilKirim.className = "status error";
    hasilKirim.textContent = `Gagal mengirim: ${galat.message}`;
  }
}

if (isiPengguna) {
  kolomCariPengguna.addEventListener("input", tampilkanPengguna);
  formKomentar.addEventListener("submit", kirimKomentar);
  muatPengguna();
}
