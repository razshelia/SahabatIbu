@extends('layouts.guest')

@section('content')
<div class="bg-white p-6 rounded-lg shadow border border-gray-200">
    <h2 class="text-xl font-bold text-gray-800 mb-1 text-center">Masuk ke Akun Anda</h2>
    <p class="text-xs text-gray-500 mb-6 text-center">Sistem Informasi Bank Sampah Sahabat Ibu</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Alamat Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('email') border-red-500 @enderror">
            @error('email')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex justify-between items-center mb-1">
                <label class="text-xs font-semibold text-gray-700 uppercase">Kata Sandi</label>
                <a href="{{ route('password.forgot.form') }}" class="text-xs text-emerald-700 hover:underline">Lupa sandi?</a>
            </div>
            <input type="password" name="password" required
                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('password') border-red-500 @enderror">
            @error('password')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium py-2 rounded text-sm transition">
            Masuk dengan Email
        </button>
    </form>

    <div class="relative my-5">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
        <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-2 text-gray-400">Atau</span></div>
    </div>

    <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-2 border border-gray-300 hover:bg-gray-50 py-2 rounded text-sm text-gray-700 font-medium transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24">
            <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.6l3.1-3.1C17.3 1.7 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.4 9 5 12 5z"/>
            <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.7-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/>
            <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3s.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 12.3 0 15.1c0 2.8.7 5.4 1.9 7.8l3.7-2.9z"/>
            <path fill="#34A853" d="M12 23.5c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.4-6.4-5.2L1.9 16.5C3.7 20.4 7.5 23.5 12 23.5z"/>
        </svg>
        Masuk lewat Akun Google
    </a>

    <p class="mt-6 text-center text-xs text-gray-600">
        Belum punya akun? <a href="{{ route('register') }}" class="text-emerald-700 font-semibold hover:underline">Daftar sekarang</a>
    </p>
</div>
@endsection
