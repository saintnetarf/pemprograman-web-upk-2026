@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
<h1 class="mb-4 text-2xl font-semibold">Tambah Data Siswa</h1>

<form action="{{ route('siswa.store') }}" method="POST" class="space-y-6">
    @csrf
    @include('siswa._form')
</form>
@endsection
