// Komponen Alpine.js yang lebih besar didaftarkan di sini lewat Alpine.data().
// Berkas ini harus dimuat SEBELUM skrip Alpine (keduanya memakai defer).
document.addEventListener("alpine:init", () => {
  // Daftar tugas: pengganti daftar minat Pertemuan 4 dengan kode jauh lebih singkat
  Alpine.data("daftarTugas", () => ({
    tugas: ["Membaca materi Pertemuan 5"],
    baru: "",
    tambah() {
      const teks = this.baru.trim();
      if (teks === "") return;
      this.tugas.push(teks);
      this.baru = "";
    },
    hapus(indeks) {
      this.tugas.splice(indeks, 1);
    },
  }));

  // Data dari API: x-init memanggil muat() saat komponen dipasang
  Alpine.data("daftarPengguna", () => ({
    pengguna: [],
    cari: "",
    memuat: true,
    galat: "",
    async muat() {
      try {
        const respons = await fetch("https://jsonplaceholder.typicode.com/users");
        if (!respons.ok) throw new Error(`Status ${respons.status}`);
        this.pengguna = await respons.json();
      } catch (e) {
        this.galat = e.message;
      } finally {
        this.memuat = false;   // selalu dijalankan, berhasil maupun gagal
      }
    },
    // getter: dihitung ulang otomatis bila pengguna atau cari berubah
    get terfilter() {
      const kata = this.cari.trim().toLowerCase();
      return this.pengguna.filter((p) => p.name.toLowerCase().includes(kata));
    },
  }));
});
