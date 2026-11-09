@extends('layouts.app')

@section('title', 'Ubah Siswa')

@section('content')
<h1 class="mb-4 text-2xl font-semibold">Ubah Data Siswa</h1>

<form action="{{ route('siswa.update', $siswa) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')
    @include('siswa._form')
</form>
@endsection
