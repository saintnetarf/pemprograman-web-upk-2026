<?php

namespace App\Exports\Template;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Support\SiswaKolom;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DataSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return SiswaKolom::kolom();
    }

    public function title(): string
    {
        return 'Data Siswa';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');

                $daftar = SiswaKolom::DAFTAR;
                $textColumns = ['nik', 'no_hp', 'kode_pos', 'latitude', 'longitude'];

                // Dropdown choices
                $kelasOptions = Kelas::orderBy('nama')->pluck('nama')->toArray();
                $dropdowns = [
                    'kelas' => $kelasOptions,
                    'status_dalam_keluarga' => Siswa::STATUS_KELUARGA,
                    'pernah_tk' => ['Ya', 'Tidak'],
                    'pernah_paud' => ['Ya', 'Tidak'],
                    'status_tinggal' => Siswa::STATUS_TINGGAL,
                    'jarak_ke_sekolah' => Siswa::JARAK,
                    'transportasi' => Siswa::TRANSPORTASI,
                    'waktu_tempuh' => Siswa::WAKTU_TEMPUH,
                ];

                foreach ($daftar as $index => $item) {
                    $colIndex = $index + 1;
                    $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                    [$nama, $wajib] = [$item[0], $item[1]];

                    // Warna judul: 1F3864 (wajib) dan 7A869A (opsional), teks tebal putih
                    $bgColor = $wajib ? '1F3864' : '7A869A';
                    $headerStyle = $sheet->getStyle("{$colLetter}1");
                    $headerStyle->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                    $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bgColor);

                    // Format Teks
                    if (in_array($nama, $textColumns, true)) {
                        $sheet->getStyle("{$colLetter}2:{$colLetter}1000")
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_TEXT);
                    }

                    // Dropdown
                    if (isset($dropdowns[$nama]) && ! empty($dropdowns[$nama])) {
                        $formula = '"' . implode(',', $dropdowns[$nama]) . '"';
                        if (strlen($formula) <= 255) {
                            $validation = new DataValidation();
                            $validation->setType(DataValidation::TYPE_LIST);
                            $validation->setErrorStyle(DataValidation::STYLE_STOP);
                            $validation->setAllowBlank(true);
                            $validation->setShowInputMessage(true);
                            $validation->setShowErrorMessage(true);
                            $validation->setErrorTitle('Input tidak valid');
                            $validation->setError('Pilih salah satu nilai dari daftar.');
                            $validation->setFormula1($formula);

                            for ($row = 2; $row <= 1000; $row++) {
                                $sheet->getCell("{$colLetter}{$row}")->setDataValidation(clone $validation);
                            }
                        }
                    }
                }
            },
        ];
    }
}

