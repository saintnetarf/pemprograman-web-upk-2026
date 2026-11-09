// Halaman Proyek: mengambil data/proyek.json dengan fetch, lalu menyaring dan menampilkannya
const wadahProyek = document.querySelector("#daftar-proyek");
const wadahFilter = document.querySelector("#filter");
const kolomCari = document.querySelector("#cari");
const statusProyek = document.querySelector("#status");

let semuaProyek = [];
let kategoriAktif = "Semua";
let kataKunci = "";

// Membuat satu kartu dengan createElement dan textContent (aman dari XSS)
function buatKartu({ judul, kategori, tahun, teknologi, deskripsi }) {
  const kartu = document.createElement("article");
  kartu.className = "kartu";

  const h3 = document.createElement("h3");
  h3.textContent = judul;

  const meta = document.createElement("p");
  const tag = document.createElement("span");
  tag.className = "tag";
  tag.textContent = `${kategori} \u00B7 ${tahun}`;
  meta.append(tag);

  const isi = document.createElement("p");
  isi.textContent = deskripsi;

  const tek = document.createElement("p");
  tek.textContent = `Teknologi: ${teknologi.join(", ")}`;

  kartu.append(h3, meta, isi, tek);
  return kartu;
}

function tampilkan() {
  const hasil = semuaProyek.filter(
    ({ kategori, judul, deskripsi }) =>
      (kategoriAktif === "Semua" || kategori === kategoriAktif) &&
      `${judul} ${deskripsi}`.toLowerCase().includes(kataKunci)
  );

  wadahProyek.replaceChildren(...hasil.map(buatKartu));
  statusProyek.className = "status";
  statusProyek.textContent = hasil.length
    ? `Menampilkan ${hasil.length} dari ${semuaProyek.length} proyek.`
    : "Tidak ada proyek yang cocok.";
}

// Tombol filter dibuat dari kategori unik: Set membuang duplikat
function buatTombolFilter() {
  const daftarKategori = ["Semua", ...new Set(semuaProyek.map((p) => p.kategori))];
  const tombol = daftarKategori.map((k) => {
    const b = document.createElement("button");
    b.type = "button";
    b.textContent = k;
    b.dataset.kategori = k;
    if (k === kategoriAktif) b.classList.add("aktif");
    return b;
  });
  wadahFilter.replaceChildren(...tombol);
}

async function muatProyek() {
  statusProyek.className = "status";
  statusProyek.textContent = "Memuat data...";
  try {
    const respons = await fetch("data/proyek.json");
    // fetch TIDAK menolak (reject) pada status 404 atau 500, jadi periksa respons.ok
    if (!respons.ok) {
      throw new Error(`Gagal memuat data (status ${respons.status})`);
    }
    semuaProyek = await respons.json();
    buatTombolFilter();
    tampilkan();
  } catch (galat) {
    statusProyek.className = "status error";
    statusProyek.textContent = `Terjadi kesalahan: ${galat.message}`;
  }
}

if (wadahProyek) {
  // event delegation untuk semua tombol filter
  wadahFilter.addEventListener("click", (e) => {
    if (!e.target.matches("button")) return;
    kategoriAktif = e.target.dataset.kategori;
    for (const b of wadahFilter.children) {
      b.classList.toggle("aktif", b === e.target);
    }
    tampilkan();
  });

  kolomCari.addEventListener("input", () => {
    kataKunci = kolomCari.value.trim().toLowerCase();
    tampilkan();
  });

  muatProyek();
}
