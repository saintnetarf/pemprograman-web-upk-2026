<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PetaController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Siswa::class);

        $total = Siswa::dapatDilihat($request->user())->count();

        return view('peta.index', compact('total'));
    }

    public function data(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Siswa::class);

        $user = $request->user();

        $features = Siswa::dapatDilihat($user)
            ->with('kelas:id,nama')
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
                    'kelas' => $s->kelas?->nama ?? '-',
                    'kelurahan' => $s->kelurahan,
                    'kecamatan' => $s->kecamatan,
                    'jarak' => $s->jarak_ke_sekolah,
                    'transportasi' => $s->transportasi,
                    'waktu_tempuh' => $s->waktu_tempuh,
                    'edit_url' => $user->can('update', $s) ? route('siswa.edit', $s) : null,
                ],
            ])
            ->values();

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
