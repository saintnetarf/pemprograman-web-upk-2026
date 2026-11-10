<?php

namespace App\Exports\Template;

use App\Support\SiswaKolom;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PetunjukSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    public function array(): array
    {
        $rows = [];

        foreach (SiswaKolom::DAFTAR as $item) {
            $rows[] = [
                $item[0],
                $item[1] ? 'Ya' : 'Tidak',
                $item[2],
                $item[3],
            ];
        }

        $rows[] = ['', '', '', ''];
        $rows[] = ['CATATAN PENTING:', '', '', ''];
        $rows[] = ['1. Mulai isi data dari baris ke-2 dan jangan mengubah atau menghapus baris judul (baris 1).', '', '', ''];
        $rows[] = ['2. Jangan mengubah format kolom Teks (NIK, No HP, Kode Pos, Latitude, Longitude) menjadi Angka.', '', '', ''];
        $rows[] = ['3. Siswa dengan NIK yang sudah ada di database akan diperbarui datanya; NIK baru akan ditambahkan sebagai siswa baru.', '', '', ''];
        $rows[] = ['4. Kolom kelas harus sesuai dengan nama kelas yang sudah terdaftar di sistem.', '', '', ''];
        $rows[] = ['5. Jika email orang tua diisi dan belum terdaftar di sistem, kolom nama orang tua wajib diisi untuk pembuatan akun.', '', '', ''];
        $rows[] = ['6. Satu akun orang tua (email yang sama) dapat digunakan untuk lebih dari satu siswa (anak).', '', '', ''];
        $rows[] = ['7. Koordinat latitude dan longitude bersifat opsional; jika diisi, gunakan titik (.) sebagai pemisah desimal.', '', '', ''];
        $rows[] = ['8. Maksimal data yang dapat diimport adalah 1.000 baris dan ukuran berkas maksimal 2 MB.', '', '', ''];

        return $rows;
    }

    public function headings(): array
    {
        return ['Kolom', 'Wajib', 'Keterangan', 'Contoh isian'];
    }

    public function title(): string
    {
        return 'Petunjuk';
    }
}

