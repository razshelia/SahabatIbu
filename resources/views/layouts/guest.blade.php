<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Bank Sampah Sahabat Ibu' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen text-gray-800 flex flex-col justify-between font-sans">
    <header class="bg-white border-b border-gray-200 py-4 px-6 shadow-sm">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <a href="/" class="text-xl font-bold text-emerald-700">Bank Sampah Sahabat Ibu</a>
            <div class="space-x-4 text-sm">
                <a href="{{ route('privacy.policy') }}" class="text-gray-600 hover:text-emerald-700">Kebijakan Privasi</a>
                @guest
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-emerald-700">Masuk</a>
                    <a href="{{ route('register') }}" class="text-emerald-700 font-semibold hover:underline">Daftar</a>
                @endguest
            </div>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center p-4">
        <div class="w-full max-w-md my-6">
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-4 text-emerald-800 text-sm rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 text-red-800 text-sm rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-4 text-amber-800 text-sm rounded">
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 text-blue-800 text-sm rounded">
                    {{ session('info') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-500">
        &copy; {{ date('Y') }} Bank Sampah Sahabat Ibu - Perumahan Taman Gading, Jember
    </footer>
</body>
</html>
