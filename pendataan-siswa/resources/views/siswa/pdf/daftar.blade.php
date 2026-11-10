<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Data Siswa</title>
    <style>
        @page {
            margin: 15mm 12mm 18mm 12mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            color: #1e293b;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0 0 4px 0;
            color: #0f172a;
        }
        .filter-info {
            font-size: 8pt;
            color: #475569;
            margin-bottom: 10px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            vertical-align: top;
        }
        table.data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            font-size: 7.5pt;
        }
        table.data-table thead {
            display: table-header-group;
        }
        table.data-table tr {
            page-break-inside: avoid;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .footer {
            position: fixed;
            bottom: -12mm;
            left: 0;
            right: 0;
            height: 8mm;
            font-size: 7pt;
            color: #64748b;
            border-top: 1px solid #cbd5e1;
            padding-top: 2mm;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-table td {
            border: none;
            padding: 0;
        }
        .page-number:after {
            content: "Halaman " counter(page);
        }
    </style>
</head>
<body>
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="text-align: left;">
                    DOKUMEN RAHASIA - berisi data pribadi siswa. Dicetak oleh {{ $pencetak }}, {{ $waktuCetak }}.
                </td>
                <td style="text-align: right; width: 80px;">
                    <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </div>

    <div class="header">
        <h1>DAFTAR DATA SISWA</h1>
        <div class="filter-info">
            Filter Kelas: <strong>{{ $filterKelas ?? 'Semua' }}</strong> |
            Pencarian: <strong>{{ $filterCari ?: '-' }}</strong> |
            Jumlah: <strong>{{ $siswas->count() }} siswa</strong>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px; text-align: center;">No</th>
                <th style="width: 85px;">NIK</th>
                <th>Nama Lengkap</th>
                <th style="width: 35px;">Kelas</th>
                <th>Orang Tua</th>
                <th>Alamat</th>
                <th>Kelurahan / Kecamatan</th>
                <th style="width: 60px;">Jarak</th>
                <th style="width: 65px;">Transportasi</th>
                <th style="width: 75px;">Koordinat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswas as $index => $s)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-family: monospace;">{{ $s->nik }}</td>
                    <td style="font-weight: bold;">{{ $s->nama_lengkap }}</td>
                    <td>{{ $s->kelas?->nama ?? '-' }}</td>
                    <td>{{ $s->orangTua?->name ?? '-' }}</td>
                    <td>{{ $s->alamat }}</td>
                    <td>{{ $s->kelurahan }}, {{ $s->kecamatan }}</td>
                    <td>{{ $s->jarak_ke_sekolah }}</td>
                    <td>{{ $s->transportasi }}</td>
                    <td>
                        @if ($s->latitude !== null && $s->longitude !== null)
                            {{ $s->latitude }}, {{ $s->longitude }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; color: #64748b; padding: 12px;">
                        Tidak ada data siswa yang sesuai.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

