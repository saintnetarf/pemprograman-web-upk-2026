@extends('layouts.app')

@section('title', $kelas->exists ? 'Ubah Kelas' : 'Tambah Kelas')

@section('content')
<div class="mx-auto max-w-xl">
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="mb-6 text-xl font-bold text-slate-800">
            {{ $kelas->exists ? 'Ubah Kelas' : 'Tambah Kelas Baru' }}
        </h1>

        <form action="{{ $kelas->exists ? route('kelas.update', $kelas) : route('kelas.store') }}" method="POST">
            @csrf
            @if ($kelas->exists)
                @method('PUT')
            @endif

            <div class="space-y-4">
                <x-form-input
                    name="nama"
                    label="Nama Kelas"
                    :value="$kelas->nama"
                    required
                />

                <x-form-select-relasi
                    name="wali_kelas_id"
                    label="Wali Kelas"
                    :options="$waliList"
                    :value="$kelas->wali_kelas_id"
                    placeholder="-- Pilih Wali Kelas (Opsional) --"
                />
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="rounded bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">
                    Simpan
                </button>
                <a href="{{ route('kelas.index') }}" class="rounded border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-100">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

