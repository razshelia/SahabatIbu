<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Bank Sampah Sahabat Ibu' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen text-gray-800 flex flex-col font-sans">
    <nav class="bg-emerald-800 text-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-3 flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center space-x-3">
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('nasabah.dashboard') }}" class="font-bold text-lg tracking-tight">
                    Bank Sampah Sahabat Ibu
                </a>
                <span class="text-xs bg-emerald-900 px-2 py-0.5 rounded uppercase tracking-wider font-semibold">
                    {{ auth()->user()->role }}
                </span>
            </div>

            <div class="flex items-center space-x-3 text-sm flex-wrap">
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-200">Dashboard</a>
                    <a href="{{ route('admin.nasabah.index') }}" class="hover:text-emerald-200">Nasabah</a>
                    <a href="{{ route('admin.nasabah.pendaftar') }}" class="hover:text-emerald-200">Pendaftar</a>
                    <a href="{{ route('admin.katalog.index') }}" class="hover:text-emerald-200">Katalog</a>
                    <a href="{{ route('admin.setoran.index') }}" class="hover:text-emerald-200">Setoran</a>
                    <a href="{{ route('admin.penarikan.index') }}" class="hover:text-emerald-200">Penarikan</a>
                    <a href="{{ route('admin.laporan.index') }}" class="hover:text-emerald-200">Laporan</a>
                    <a href="{{ route('admin.profile.show') }}" class="hover:text-emerald-200">Profil & Log</a>
                @elseif(auth()->user()->role === 'nasabah')
                    <a href="{{ route('nasabah.dashboard') }}" class="hover:text-emerald-200">Dashboard</a>
                    <a href="{{ route('nasabah.setoran.index') }}" class="hover:text-emerald-200">Riwayat Setoran</a>
                    <a href="{{ route('nasabah.penarikan.index') }}" class="hover:text-emerald-200">Tarik Dana</a>
                    <a href="{{ route('nasabah.katalog.index') }}" class="hover:text-emerald-200">Katalog Harga</a>
                    <a href="{{ route('nasabah.profile.show') }}" class="hover:text-emerald-200">Profil Saya</a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="inline ml-2">
                    @csrf
                    <button type="submit" class="bg-emerald-950 hover:bg-black text-xs px-3 py-1.5 rounded transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-6xl w-full mx-auto px-4 py-6">
        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-5 text-emerald-800 text-sm rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-5 text-red-800 text-sm rounded shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-5 text-amber-800 text-sm rounded shadow-sm">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-5 text-blue-800 text-sm rounded shadow-sm">
                {{ session('info') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-500">
        <div class="max-w-6xl mx-auto px-4 flex justify-between items-center">
            <span>&copy; {{ date('Y') }} Bank Sampah Sahabat Ibu</span>
            <a href="{{ route('privacy.policy') }}" class="hover:underline">Kebijakan Privasi</a>
        </div>
    </footer>
</body>
</html>
