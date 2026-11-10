@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
<div class="mx-auto max-w-xl">
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="mb-6 text-xl font-bold text-slate-800">Tambah Pengguna Baru</h1>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            @include('users._form')
        </form>
    </div>
</div>
@endsection

