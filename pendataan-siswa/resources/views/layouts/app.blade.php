<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pendataan Siswa') - SIG Pendataan Siswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800">
    <header class="border-b bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
            <a href="{{ route('peta') }}" class="text-lg font-semibold">
                SIG Pendataan Siswa SMA
            </a>
            <nav class="flex gap-4 text-sm">
                <a href="{{ route('peta') }}" class="hover:text-blue-600">Peta</a>
                <a href="{{ route('siswa.index') }}" class="hover:text-blue-600">
                    Data Siswa (Admin)
                </a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6">
        @if (session('success'))
            <div class="mb-4 rounded bg-green-100 px-4 py-2 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
