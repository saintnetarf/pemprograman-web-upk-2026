<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Biodata Siswa - {{ $siswa->nama_lengkap }}</title>
    <style>
        @page {
            margin: 18mm 15mm 20mm 15mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .header h1 {
            font-size: 15pt;
            font-weight: bold;
            margin: 0;
            color: #0f172a;
        }
        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            color: #1e3a8a;
            background-color: #f1f5f9;
            padding: 4px 8px;
            margin-top: 14px;
            margin-bottom: 6px;
            border-left: 4px solid #1e3a8a;
        }
        table.biodata-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.biodata-table td {
            padding: 4px 6px;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }
        table.biodata-table td.label {
            width: 180px;
            color: #475569;
            font-weight: 500;
        }
        table.biodata-table td.separator {
            width: 10px;
            text-align: center;
            color: #94a3b8;
        }
        table.biodata-table td.value {
            color: #0f172a;
        }
        .footer {
            position: fixed;
            bottom: -14mm;
            left: 0;
            right: 0;
            height: 8mm;
            font-size: 7.5pt;
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
        <h1>BIODATA SISWA</h1>
    </div>

    <div class="section-title">A. Data Pribadi</div>
    <table class="biodata-table">
        <tr>
            <td class="label">NIK</td>
            <td class="separator">:</td>
            <td class="value" style="font-family: monospace; font-weight: bold;">{{ $siswa->nik }}</td>
        </tr>
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="separator">:</td>
            <td class="value" style="font-weight: bold;">{{ $siswa->nama_lengkap }}</td>
        </tr>
        <tr>
            <td class="label">Status dalam Keluarga</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->status_dalam_keluarga }}</td>
        </tr>
        <tr>
            <td class="label">Anak ke / Jumlah Saudara</td>
            <td class="separator">:</td>
            <td class="value">Anak ke-{{ $siswa->anak_ke }} (dari {{ $siswa->jumlah_saudara }} bersaudara)</td>
        </tr>
        <tr>
            <td class="label">Pernah TK</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->pernah_tk ? 'Ya' : 'Tidak' }}</td>
        </tr>
        <tr>
            <td class="label">Pernah PAUD</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->pernah_paud ? 'Ya' : 'Tidak' }}</td>
        </tr>
        <tr>
            <td class="label">Hobi</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->hobi }}</td>
        </tr>
        <tr>
            <td class="label">Cita-cita</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->cita_cita }}</td>
        </tr>
        <tr>
            <td class="label">Nomor HP / WhatsApp</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->no_hp }}</td>
        </tr>
    </table>

    <div class="section-title">B. Kelas dan Orang Tua</div>
    <table class="biodata-table">
        <tr>
            <td class="label">Kelas</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->kelas?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Wali Kelas</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->kelas?->waliKelas?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Orang Tua / Wali</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->orangTua?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Email Orang Tua</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->orangTua?->email ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">C. Alamat Tempat Tinggal</div>
    <table class="biodata-table">
        <tr>
            <td class="label">Alamat Lengkap</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->alamat }}</td>
        </tr>
        <tr>
            <td class="label">Kelurahan / Desa</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->kelurahan }}</td>
        </tr>
        <tr>
            <td class="label">Kecamatan</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->kecamatan }}</td>
        </tr>
        <tr>
            <td class="label">Kabupaten / Kota</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->kabupaten_kota }}</td>
        </tr>
        <tr>
            <td class="label">Kode Pos</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->kode_pos }}</td>
        </tr>
        <tr>
            <td class="label">Status Tempat Tinggal</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->status_tinggal }}</td>
        </tr>
    </table>

    <div class="section-title">D. Jarak dan Transportasi</div>
    <table class="biodata-table">
        <tr>
            <td class="label">Jarak ke Sekolah</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->jarak_ke_sekolah }}</td>
        </tr>
        <tr>
            <td class="label">Transportasi Utama</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->transportasi }}</td>
        </tr>
        <tr>
            <td class="label">Waktu Tempuh</td>
            <td class="separator">:</td>
            <td class="value">{{ $siswa->waktu_tempuh }}</td>
        </tr>
    </table>

    <div class="section-title">E. Titik Koordinat Rumah</div>
    <table class="biodata-table">
        <tr>
            <td class="label">Titik Koordinat (Lat, Long)</td>
            <td class="separator">:</td>
            <td class="value">
                @if ($siswa->latitude !== null && $siswa->longitude !== null)
                    {{ $siswa->latitude }}, {{ $siswa->longitude }}
                    <br>
                    <a href="https://www.openstreetmap.org/?mlat={{ $siswa->latitude }}&amp;mlon={{ $siswa->longitude }}#map=17/{{ $siswa->latitude }}/{{ $siswa->longitude }}"
                       style="color: #2563eb; text-decoration: underline; font-size: 8pt;">
                        Buka di OpenStreetMap
                    </a>
                @else
                    Belum ditentukan
                @endif
            </td>
        </tr>
    </table>
</body>
</html>

