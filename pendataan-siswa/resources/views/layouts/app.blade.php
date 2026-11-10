@use('App\Enums\Role')
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
            <a href="{{ url('/') }}" class="text-lg font-semibold text-slate-800">
                SIG Pendataan Siswa SMA
            </a>
            <nav class="flex items-center gap-4 text-sm">
                @auth
                    @php($user = auth()->user())
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Beranda</a>

                    {{-- MENU-ROLE --}}
                    @if ($user->hasRole(Role::Sekolah, Role::WaliKelas, Role::OrangTua, Role::Siswa))
                        <a href="{{ route('peta') }}" class="hover:text-blue-600">Peta</a>
                    @endif

                    @if ($user->hasRole(Role::Sekolah, Role::WaliKelas, Role::OrangTua))
                        <a href="{{ route('siswa.index') }}" class="hover:text-blue-600">Data Siswa</a>
                    @endif

                    @if ($user->hasRole(Role::Siswa))
                        <a href="{{ route('profil') }}" class="hover:text-blue-600">Profil Saya</a>
                    @endif

                    @if ($user->hasRole(Role::Sekolah))
                        <a href="{{ route('kelas.index') }}" class="hover:text-blue-600">Kelas</a>
                    @endif

                    @if ($user->hasRole(Role::Admin))
                        <a href="{{ route('users.index') }}" class="hover:text-blue-600">Pengguna</a>
                    @endif

                    <div class="ml-4 flex items-center gap-3 border-l border-slate-200 pl-4">
                        <span class="text-slate-600 font-medium">
                            {{ $user->name }} <span class="text-xs text-slate-500">({{ $user->role->label() }})</span>
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hover:text-blue-600 font-medium">Masuk</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6">
        @if (session('success'))
            <div class="mb-4 rounded bg-green-100 px-4 py-2 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded bg-red-100 px-4 py-2 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
