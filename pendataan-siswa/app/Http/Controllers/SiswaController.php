<?php

namespace App\Http\Controllers;

use App\Http\Requests\SiswaRequest;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->query('q');

        $siswas = Siswa::query()
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
        return view('siswa.create', ['siswa' => new Siswa()]);
    }

    public function store(SiswaRequest $request): RedirectResponse
    {
        Siswa::create($request->validated());

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa): View
    {
        return view('siswa.edit', compact('siswa'));
    }

    public function update(SiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        $siswa->update($request->validated());

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        $siswa->delete();

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
