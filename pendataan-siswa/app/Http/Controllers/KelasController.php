<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(): View
    {
        $kelasList = Kelas::with('waliKelas')
            ->withCount('siswas')
            ->orderBy('nama')
            ->get();

        return view('kelas.index', compact('kelasList'));
    }

    public function create(): View
    {
        $waliList = User::where('role', Role::WaliKelas)->pluck('name', 'id');

        return view('kelas.form', [
            'kelas' => new Kelas(),
            'waliList' => $waliList,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:30', 'unique:kelas,nama'],
            'wali_kelas_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', Role::WaliKelas->value),
            ],
        ]);

        Kelas::create($validated);

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas): View
    {
        $waliList = User::where('role', Role::WaliKelas)->pluck('name', 'id');

        return view('kelas.form', [
            'kelas' => $kelas,
            'waliList' => $waliList,
        ]);
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:30',
                Rule::unique('kelas', 'nama')->ignore($kelas->id),
            ],
            'wali_kelas_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', Role::WaliKelas->value),
            ],
        ]);

        $kelas->update($validated);

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        if ($kelas->siswas()->exists()) {
            return back()->with('error', 'Kelas masih memiliki siswa. Pindahkan siswa terlebih dahulu.');
        }

        $kelas->delete();

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}

