@extends('layouts.guest')

@section('content')
<div class="bg-white p-6 rounded-lg shadow border border-gray-200">
    <h2 class="text-xl font-bold text-gray-800 mb-1 text-center">Lupa Kata Sandi</h2>
    <p class="text-xs text-gray-500 mb-6 text-center">Masukkan email Anda untuk menerima tautan pemulihan sandi</p>

    @if(session('status'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-3 mb-4 text-emerald-800 text-xs rounded">
            {{ session('status') }}
        </div>
    @endif

    @if(session('demo_reset_link'))
        <div class="bg-blue-50 border border-blue-200 p-3 mb-4 rounded text-xs text-blue-900">
            <strong>Tautan Cepat (Uji Lokal):</strong><br>
            <a href="{{ session('demo_reset_link') }}" class="underline text-blue-700 break-all">{{ session('demo_reset_link') }}</a>
        </div>
    @endif

    <form method="POST" action="{{ route('password.forgot.send') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Alamat Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('email') border-red-500 @enderror">
            @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium py-2 rounded text-sm transition">
            Kirim Tautan Reset Sandi
        </button>
    </form>

    <p class="mt-6 text-center text-xs text-gray-600">
        Ingat kata sandi Anda? <a href="{{ route('login') }}" class="text-emerald-700 font-semibold hover:underline">Kembali ke halaman masuk</a>
    </p>
</div>
@endsection
