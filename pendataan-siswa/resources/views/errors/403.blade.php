@extends('layouts.app')

@section('title', '403 - Akses Ditolak')

@section('content')
<div class="mx-auto max-w-lg text-center">
    <div class="rounded-lg border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-6xl font-extrabold text-red-600">403</h1>
        <h2 class="mt-4 text-xl font-bold text-slate-800">Akses ditolak</h2>
        <p class="mt-2 text-sm text-slate-600">
            Anda tidak memiliki hak akses ke halaman atau data ini.
        </p>
        <div class="mt-6">
            <a href="{{ route('dashboard') }}" class="inline-block rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection

