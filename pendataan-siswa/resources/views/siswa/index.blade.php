@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-semibold text-slate-800">Data Siswa</h1>
    <div class="flex flex-wrap items-center gap-2">
        @can('import', App\Models\Siswa::class)
            <a href="{{ route('siswa.import.create') }}"
               class="rounded bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                Import Excel
            </a>
        @endcan

        @can('export', App\Models\Siswa::class)
            <a href="{{ route('siswa.ekspor.daftar', request()->only('q', 'kelas')) }}"
               class="rounded bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">
                Ekspor PDF
            </a>
        @endcan

        @can('create', App\Models\Siswa::class)
            <a href="{{ route('siswa.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                + Tambah Siswa
            </a>
        @endcan
    </div>
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2">
    @if ($pilihanKelas->isNotEmpty())
        <select name="kelas" class="rounded border border-slate-300 bg-white px-3 py-2 text-sm">
            <option value="">-- Semua Kelas --</option>
            @foreach ($pilihanKelas as $id => $namaKelas)
                <option value="{{ $id }}" @selected((string) $kelasId === (string) $id)>
                    {{ $namaKelas }}
                </option>
            @endforeach
        </select>
    @endif

    <input type="text" name="q" value="{{ $q }}"
           placeholder="Cari nama, NIK, atau kecamatan..."
           class="w-full max-w-sm rounded border border-slate-300 px-3 py-2 text-sm">
    <button class="rounded bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-800">Cari</button>
</form>

<div class="overflow-x-auto rounded border bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-100 text-left">
            <tr>
                <th class="px-3 py-2">No</th>
                <th class="px-3 py-2">Nama Lengkap</th>
                <th class="px-3 py-2">Kelas</th>
                <th class="px-3 py-2">NIK</th>
                <th class="px-3 py-2">Kecamatan</th>
                <th class="px-3 py-2">Koordinat (lat, lng)</th>
                <th class="px-3 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswas as $siswa)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $siswas->firstItem() + $loop->index }}</td>
                    <td class="px-3 py-2 font-medium">{{ $siswa->nama_lengkap }}</td>
                    <td class="px-3 py-2">{{ $siswa->kelas?->nama ?? '-' }}</td>
                    <td class="px-3 py-2 font-mono">{{ $siswa->nik }}</td>
                    <td class="px-3 py-2">{{ $siswa->kecamatan }}</td>
                    <td class="px-3 py-2">
                        @if ($siswa->latitude !== null && $siswa->longitude !== null)
                            {{ $siswa->latitude }}, {{ $siswa->longitude }}
                        @else
                            <span class="inline-flex items-center rounded bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">
                                Belum ada titik
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        @can('view', $siswa)
                            <a href="{{ route('siswa.ekspor.biodata', $siswa) }}" target="_blank"
                               class="mr-2 text-rose-600 hover:underline">PDF</a>
                        @endcan
                        @can('update', $siswa)
                            <a href="{{ route('siswa.edit', $siswa) }}"
                               class="text-blue-600 hover:underline">Ubah</a>
                        @endcan
                        @can('delete', $siswa)
                            <form action="{{ route('siswa.destroy', $siswa) }}" method="POST"
                                  class="ml-2 inline"
                                  onsubmit="return confirm('Hapus data siswa ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-3 py-6 text-center text-slate-500">
                        Belum ada data siswa.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $siswas->links() }}
</div>
@endsection
