@extends('layouts.app')

@section('title', 'Import Data Siswa')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-semibold text-slate-800">Import Data Siswa</h1>
        <p class="mt-1 text-sm text-slate-600">
            Unggah berkas Excel untuk menambahkan atau memperbarui data siswa secara massal.
        </p>
    </div>
    <a href="{{ route('siswa.index') }}" class="rounded border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
        &larr; Kembali ke Data Siswa
    </a>
</div>

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    {{-- Kartu 1: Unduh Template --}}
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800">1. Unduh Template Excel</h2>
        <p class="mt-2 text-sm text-slate-600">
            Gunakan template resmi yang telah dilengkapi dengan format teks, petunjuk pengisian, dan daftar pilihan (dropdown).
        </p>
        <div class="mt-6">
            <a href="{{ route('siswa.import.template') }}"
               class="inline-flex items-center gap-2 rounded bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Unduh Template (.xlsx)
            </a>
        </div>
    </div>

    {{-- Kartu 2: Form Unggah --}}
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800">2. Unggah Berkas Excel</h2>
        <form action="{{ route('siswa.import.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf

            <div>
                <label for="file" class="block text-sm font-medium text-slate-700">Pilih Berkas Excel (.xlsx)</label>
                <input type="file" name="file" id="file" accept=".xlsx"
                       class="mt-1 block w-full rounded border border-slate-300 p-2 text-sm focus:border-blue-500 focus:outline-none" required>
                @error('file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-start">
                <input type="checkbox" name="periksa_saja" id="periksa_saja" value="1"
                       @checked(old('periksa_saja', true))
                       class="mt-1 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="periksa_saja" class="ml-2 block text-sm text-slate-700">
                    <span class="font-medium">Periksa saja (simulasi / dry-run)</span>
                    <span class="block text-xs text-slate-500">
                        Cek validitas berkas tanpa menyimpan data atau membuat akun baru di database.
                    </span>
                </label>
            </div>

            <button type="submit"
                    class="rounded bg-blue-600 px-5 py-2 font-medium text-white hover:bg-blue-700 focus:outline-none">
                Proses Berkas
            </button>
        </form>
    </div>
</div>

{{-- Hasil Import --}}
@if ($hasil !== null)
    <div class="mt-8 space-y-6">
        {{-- Kartu Ringkasan --}}
        <div class="rounded-lg border p-6 {{ $hasil['dry_run'] ? 'border-blue-200 bg-blue-50 text-blue-900' : 'border-green-200 bg-green-50 text-green-900' }}">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-lg font-bold">
                    @if ($hasil['dry_run'])
                        Simulasi Selesai (Periksa Saja - belum ada data yang disimpan)
                    @else
                        Import selesai
                    @endif
                </h3>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded bg-white/70 p-3 shadow-sm">
                    <span class="block text-xs font-medium text-slate-500">Total Baris</span>
                    <span class="text-2xl font-bold">{{ $hasil['total'] }}</span>
                </div>
                <div class="rounded bg-white/70 p-3 shadow-sm">
                    <span class="block text-xs font-medium text-slate-500">
                        {{ $hasil['dry_run'] ? 'Akan dibuat' : 'Dibuat' }}
                    </span>
                    <span class="text-2xl font-bold text-emerald-700">{{ $hasil['dibuat'] }}</span>
                </div>
                <div class="rounded bg-white/70 p-3 shadow-sm">
                    <span class="block text-xs font-medium text-slate-500">
                        {{ $hasil['dry_run'] ? 'Akan diperbarui' : 'Diperbarui' }}
                    </span>
                    <span class="text-2xl font-bold text-blue-700">{{ $hasil['diperbarui'] }}</span>
                </div>
                <div class="rounded bg-white/70 p-3 shadow-sm">
                    <span class="block text-xs font-medium text-slate-500">Gagal</span>
                    <span class="text-2xl font-bold {{ $hasil['gagal'] > 0 ? 'text-red-700' : 'text-slate-700' }}">
                        {{ $hasil['gagal'] }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Tabel Galat --}}
        @if (! empty($hasil['galat']))
            <div class="rounded-lg border border-red-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-red-800">Baris yang bermasalah</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-red-50 text-left">
                            <tr>
                                <th class="px-3 py-2 text-red-900">Baris</th>
                                <th class="px-3 py-2 text-red-900">Nama Siswa</th>
                                <th class="px-3 py-2 text-red-900">Daftar Pesan Galat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($hasil['galat'] as $g)
                                <tr>
                                    <td class="px-3 py-2 font-mono font-bold text-slate-700">{{ $g['baris'] }}</td>
                                    <td class="px-3 py-2 font-medium text-slate-800">{{ $g['nama'] }}</td>
                                    <td class="px-3 py-2 text-red-700">
                                        <ul class="list-inside list-disc space-y-1">
                                            @foreach ($g['pesan'] as $p)
                                                <li>{{ $p }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Tabel Akun Orang Tua Baru --}}
        @if (! empty($hasil['akun_baru']))
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-800">Akun Orang Tua Baru</h3>

                @if (! $hasil['dry_run'])
                    <div class="mt-2 rounded bg-amber-50 p-3 text-sm text-amber-800 border border-amber-200">
                        <strong>Perhatian:</strong> Catat atau salin password akun orang tua di bawah ini sekarang. Password ini diacak dan hanya ditampilkan sekali ini saja demi keamanan.
                    </div>
                @endif

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-left">
                            <tr>
                                <th class="px-3 py-2">Nama</th>
                                <th class="px-3 py-2">Email</th>
                                <th class="px-3 py-2">Password</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($hasil['akun_baru'] as $a)
                                <tr>
                                    <td class="px-3 py-2 font-medium">{{ $a['nama'] }}</td>
                                    <td class="px-3 py-2">{{ $a['email'] }}</td>
                                    <td class="px-3 py-2 font-mono">
                                        @if ($a['password'] !== null)
                                            <span class="rounded bg-slate-100 px-2 py-0.5 text-slate-800 font-semibold">{{ $a['password'] }}</span>
                                        @else
                                            <span class="italic text-slate-500">dibuat saat import sungguhan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div>
            <a href="{{ route('siswa.index') }}" class="inline-block rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Lihat Data Siswa &rarr;
            </a>
        </div>
    </div>
@endif
@endsection
