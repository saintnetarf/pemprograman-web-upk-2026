@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-semibold">Data Siswa</h1>
    @can('create', App\Models\Siswa::class)
        <a href="{{ route('siswa.create') }}"
           class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
            + Tambah Siswa
        </a>
    @endcan
</div>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="q" value="{{ $q }}"
           placeholder="Cari nama, NIK, atau kecamatan..."
           class="w-full max-w-sm rounded border border-slate-300 px-3 py-2">
    <button class="rounded bg-slate-700 px-4 py-2 text-white">Cari</button>
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
                    <td class="px-3 py-2">{{ $siswa->nama_lengkap }}</td>
                    <td class="px-3 py-2">{{ $siswa->kelas?->nama ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $siswa->nik }}</td>
                    <td class="px-3 py-2">{{ $siswa->kecamatan }}</td>
                    <td class="px-3 py-2">{{ $siswa->latitude }}, {{ $siswa->longitude }}</td>
                    <td class="px-3 py-2">
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
