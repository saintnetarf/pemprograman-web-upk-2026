// Modul pembantu: dipakai lewat import pada js/es6-latihan.js
// Named export: banyak per berkas, diimpor dengan kurung kurawal
export const kapital = (teks) => teks.charAt(0).toUpperCase() + teks.slice(1);

// Rest parameter: ...angka mengumpulkan semua argumen menjadi satu array
export function hitungRata(...angka) {
  if (angka.length === 0) return 0;
  return angka.reduce((jumlah, a) => jumlah + a, 0) / angka.length;
}

export const formatRupiah = (angka) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(angka);

// Default export: satu per berkas, diimpor tanpa kurung kurawal
export default function ringkas(teks, maks = 40) {
  return teks.length > maks ? teks.slice(0, maks - 3) + "..." : teks;
}
