@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-semibold">Data Kelas</h1>
    <a href="{{ route('kelas.create') }}"
       class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
        + Tambah Kelas
    </a>
</div>

<div class="overflow-x-auto rounded border bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-100 text-left">
            <tr>
                <th class="px-3 py-2">No</th>
                <th class="px-3 py-2">Kelas</th>
                <th class="px-3 py-2">Wali Kelas</th>
                <th class="px-3 py-2">Jumlah Siswa</th>
                <th class="px-3 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kelasList as $kelas)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $loop->iteration }}</td>
                    <td class="px-3 py-2 font-medium">{{ $kelas->nama }}</td>
                    <td class="px-3 py-2">{{ $kelas->waliKelas?->name ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $kelas->siswas_count }} siswa</td>
                    <td class="px-3 py-2">
                        <a href="{{ route('kelas.edit', $kelas) }}"
                           class="text-blue-600 hover:underline">Ubah</a>
                        <form action="{{ route('kelas.destroy', $kelas) }}" method="POST"
                              class="ml-2 inline"
                              onsubmit="return confirm('Hapus kelas ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-3 py-6 text-center text-slate-500">
                        Belum ada data kelas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

