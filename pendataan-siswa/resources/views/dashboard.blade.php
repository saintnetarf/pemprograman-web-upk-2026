@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="space-y-6">
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-800">
            Selamat datang, {{ $user->name }}!
        </h1>
        <p class="mt-1 text-sm text-slate-600">
            Anda masuk sebagai <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-800">{{ $user->role->label() }}</span>.
        </p>

        <div class="mt-6 border-t border-slate-200 pt-4">
            <h2 class="text-base font-semibold text-slate-800">Hak Akses Anda:</h2>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-slate-600">
                @foreach ($hakAkses as $akses)
                    <li>{{ $akses }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection

