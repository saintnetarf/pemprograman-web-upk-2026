<?php

namespace App\Http\Controllers;

use App\Exports\SiswaTemplateExport;
use App\Imports\SiswaImport;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SiswaImportController extends Controller
{
    public function create(): View
    {
        Gate::authorize('import', Siswa::class);

        return view('siswa.import', ['hasil' => null]);
    }

    public function template(): BinaryFileResponse
    {
        Gate::authorize('import', Siswa::class);

        return Excel::download(new SiswaTemplateExport(), 'template-import-siswa.xlsx');
    }

    public function store(Request $request): View
    {
        Gate::authorize('import', Siswa::class);

        $request->validate([
            'file' => ['required', 'file', 'extensions:xlsx', 'max:2048'],
        ], [
            'file.required' => 'Pilih berkas Excel yang akan diunggah.',
            'file.file' => 'Berkas unggahan tidak valid.',
            'file.extensions' => 'Berkas harus berformat .xlsx.',
            'file.max' => 'Ukuran berkas maksimal 2 MB (2048 KB).',
        ]);

        $import = new SiswaImport($request->boolean('periksa_saja'));

        try {
            Excel::import($import, $request->file('file'));
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors([
                'file' => 'Berkas tidak dapat dibaca. Pastikan berkas adalah Excel (.xlsx) yang valid.',
            ]);
        }

        return view('siswa.import', ['hasil' => $import->hasil]);
    }
}

