<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\SiswaRequest;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Siswa::class);

        $q = $request->query('q');

        $siswas = Siswa::query()
            ->dapatDilihat($request->user())
            ->with('kelas')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nama_lengkap', 'like', "%{$q}%")
                      ->orWhere('nik', 'like', "%{$q}%")
                      ->orWhere('kecamatan', 'like', "%{$q}%");
                });
            })
            ->orderBy('nama_lengkap')
            ->paginate(10)
            ->withQueryString();

        return view('siswa.index', compact('siswas', 'q'));
    }

    public function create(): View
    {
        Gate::authorize('create', Siswa::class);

        return view('siswa.create', $this->dataForm(new Siswa()));
    }

    public function store(SiswaRequest $request): RedirectResponse
    {
        Siswa::create($request->validated());

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa): View
    {
        Gate::authorize('update', $siswa);

        return view('siswa.edit', $this->dataForm($siswa));
    }

    public function update(SiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        $siswa->update($request->validated());

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        Gate::authorize('delete', $siswa);

        $siswa->delete();

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }

    public function profil(Request $request): RedirectResponse
    {
        $siswa = $request->user()->siswaProfil;

        abort_if(! $siswa, 404, 'Profil siswa belum ditautkan ke akun Anda.');

        return redirect()->route('siswa.edit', $siswa);
    }

    private function dataForm(Siswa $siswa): array
    {
        return [
            'siswa' => $siswa,
            'daftarKelas' => Kelas::orderBy('nama')->pluck('nama', 'id'),
            'akunSiswa' => User::where('role', Role::Siswa)->orderBy('name')->pluck('name', 'id'),
            'akunOrtu' => User::where('role', Role::OrangTua)->orderBy('name')->pluck('name', 'id'),
        ];
    }
}
