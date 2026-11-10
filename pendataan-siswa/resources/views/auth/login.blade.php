@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="mx-auto max-w-md">
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="mb-6 text-center text-xl font-bold text-slate-800">Masuk ke Sistem</h1>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <x-form-input
                name="email"
                label="Email"
                type="email"
                :value="old('email')"
                required
                autofocus
            />

            <x-form-input
                name="password"
                label="Password"
                type="password"
                required
            />

            <div class="flex items-center">
                <input
                    type="checkbox"
                    name="remember"
                    id="remember"
                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                >
                <label for="remember" class="ml-2 block text-sm text-slate-700">
                    Ingat saya
                </label>
            </div>

            <button
                type="submit"
                class="w-full rounded bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700 focus:outline-none"
            >
                Masuk
            </button>
        </form>
    </div>
</div>
@endsection

