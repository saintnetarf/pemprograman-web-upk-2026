@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-semibold">Kelola Pengguna</h1>
    <a href="{{ route('users.create') }}"
       class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
        + Tambah Pengguna
    </a>
</div>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="q" value="{{ $q }}"
           placeholder="Cari nama atau email..."
           class="w-full max-w-sm rounded border border-slate-300 px-3 py-2">
    <button class="rounded bg-slate-700 px-4 py-2 text-white">Cari</button>
</form>

<div class="overflow-x-auto rounded border bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-100 text-left">
            <tr>
                <th class="px-3 py-2">No</th>
                <th class="px-3 py-2">Nama</th>
                <th class="px-3 py-2">Email</th>
                <th class="px-3 py-2">Peran</th>
                <th class="px-3 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $u)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $users->firstItem() + $loop->index }}</td>
                    <td class="px-3 py-2">{{ $u->name }}</td>
                    <td class="px-3 py-2">{{ $u->email }}</td>
                    <td class="px-3 py-2">
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-800">
                            {{ $u->role->label() }}
                        </span>
                    </td>
                    <td class="px-3 py-2">
                        <a href="{{ route('users.edit', $u) }}"
                           class="text-blue-600 hover:underline">Ubah</a>
                        @if (! $u->is(auth()->user()))
                            <form action="{{ route('users.destroy', $u) }}" method="POST"
                                  class="ml-2 inline"
                                  onsubmit="return confirm('Hapus pengguna ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-3 py-6 text-center text-slate-500">
                        Belum ada data pengguna.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection

