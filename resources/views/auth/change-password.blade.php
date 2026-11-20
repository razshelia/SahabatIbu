@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow border border-gray-200">
    <h2 class="text-xl font-bold text-gray-800 mb-1">
        {{ $isMustChange ? 'Ganti Kata Sandi Pertama Kali' : 'Ubah Kata Sandi' }}
    </h2>
    <p class="text-xs text-gray-500 mb-6">
        {{ $isMustChange ? 'Demi keamanan akun Anda, Anda wajib mengganti kata sandi awal sebelum dapat mengakses fitur sistem.' : 'Perbarui kata sandi akun Anda secara berkala.' }}
    </p>

    <form method="POST" action="{{ route('password.change.update') }}" class="space-y-4">
        @csrf

        @if(! $isMustChange)
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('current_password') border-red-500 @enderror">
                @error('current_password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        @endif

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kata Sandi Baru</label>
            <input type="password" name="password" required placeholder="Min. 8 karakter (huruf & angka)"
                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('password') border-red-500 @enderror">
            @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Konfirmasi Kata Sandi Baru</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600">
        </div>

        <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium py-2 rounded text-sm transition">
            Simpan Kata Sandi
        </button>
    </form>
</div>
@endsection
