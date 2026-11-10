<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class SiswaExportController extends Controller
{
    public function daftar(Request $request): Response|RedirectResponse
    {
        Gate::authorize('export', Siswa::class);

        $user = $request->user();
        $q = $request->query('q');
        $kelasId = $request->query('kelas');

        $query = Siswa::query()
            ->dapatDilihat($user)
            ->cari($q)
            ->diKelas($kelasId);

        if ($query->count() > 500) {
            return back()->with('error', 'Data terlalu banyak untuk satu PDF (maksimal 500 siswa). Gunakan filter kelas.');
        }

        $siswas = $query->with(['kelas', 'orangTua'])
            ->orderBy('nama_lengkap')
            ->get();

        $filterKelas = null;
        if ($kelasId) {
            $filterKelas = Kelas::find($kelasId)?->nama;
        }

        $waktuCetak = now()->timezone('Asia/Makassar')->locale('id')->translatedFormat('d F Y, H:i') . ' WITA';

        $pdf = Pdf::loadView('siswa.pdf.daftar', [
            'siswas' => $siswas,
            'filterKelas' => $filterKelas,
            'filterCari' => $q,
            'pencetak' => $user->name,
            'waktuCetak' => $waktuCetak,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('data-siswa-' . now()->format('Ymd-His') . '.pdf');
    }

    public function biodata(Request $request, Siswa $siswa): Response
    {
        Gate::authorize('view', $siswa);

        $siswa->load(['kelas.waliKelas', 'orangTua']);

        $waktuCetak = now()->timezone('Asia/Makassar')->locale('id')->translatedFormat('d F Y, H:i') . ' WITA';

        $pdf = Pdf::loadView('siswa.pdf.biodata', [
            'siswa' => $siswa,
            'pencetak' => $request->user()->name,
            'waktuCetak' => $waktuCetak,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('biodata-' . Str::slug($siswa->nama_lengkap) . '.pdf');
    }
}

