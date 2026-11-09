<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PetaController extends Controller
{
    public function index(): View
    {
        return view('peta.index', ['total' => Siswa::count()]);
    }

    public function data(): JsonResponse
    {
        // Hanya kolom yang diperlukan peta; NIK, HP, dan alamat tidak dikirim
        $features = Siswa::select([
                'id', 'nama_lengkap', 'kelurahan', 'kecamatan',
                'jarak_ke_sekolah', 'transportasi', 'waktu_tempuh',
                'latitude', 'longitude',
            ])
            ->get()
            ->map(fn ($s) => [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    // GeoJSON: urutan [bujur, lintang]
                    'coordinates' => [$s->longitude, $s->latitude],
                ],
                'properties' => [
                    'nama' => $s->nama_lengkap,
                    'kelurahan' => $s->kelurahan,
                    'kecamatan' => $s->kecamatan,
                    'jarak' => $s->jarak_ke_sekolah,
                    'transportasi' => $s->transportasi,
                    'waktu_tempuh' => $s->waktu_tempuh,
                ],
            ])
            ->values();

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}

