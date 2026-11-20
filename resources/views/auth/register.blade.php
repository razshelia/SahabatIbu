@extends('layouts.guest')

@section('content')
<div class="bg-white p-6 rounded-lg shadow border border-gray-200">
    <h2 class="text-xl font-bold text-gray-800 mb-1 text-center">Pendaftaran Nasabah Baru</h2>
    <p class="text-xs text-gray-500 mb-6 text-center">Daftarkan diri Anda untuk mulai menabung sampah</p>

    <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-2 border border-gray-300 hover:bg-gray-50 py-2 rounded text-sm text-gray-700 font-medium transition mb-5">
        <svg class="w-4 h-4" viewBox="0 0 24 24">
            <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.6l3.1-3.1C17.3 1.7 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.4 9 5 12 5z"/>
            <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.7-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/>
            <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3s.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 12.3 0 15.1c0 2.8.7 5.4 1.9 7.8l3.7-2.9z"/>
            <path fill="#34A853" d="M12 23.5c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.4-6.4-5.2L1.9 16.5C3.7 20.4 7.5 23.5 12 23.5z"/>
        </svg>
        Daftar Cepat dengan Google
    </a>

    <div class="relative my-4">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
        <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-2 text-gray-400">Atau Isi Formulir</span></div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-3">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('name') border-red-500 @enderror">
            @error('name') <p class="text-red-600 text-xs mt-0.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Alamat Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('email') border-red-500 @enderror">
            @error('email') <p class="text-red-600 text-xs mt-0.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nomor Telepon / WhatsApp</label>
            <input type="text" name="telepon" value="{{ old('telepon') }}" placeholder="Contoh: 08123456789" required
                   class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('telepon') border-red-500 @enderror">
            @error('telepon') <p class="text-red-600 text-xs mt-0.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Alamat Rumah</label>
            <textarea name="alamat" rows="2" required placeholder="Alamat RT/RW di lingkungan Taman Gading"
                      class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('alamat') border-red-500 @enderror">{{ old('alamat') }}</textarea>
            @error('alamat') <p class="text-red-600 text-xs mt-0.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kata Sandi</label>
            <input type="password" name="password" required placeholder="Min. 8 karakter (huruf & angka)"
                   class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('password') border-red-500 @enderror">
            @error('password') <p class="text-red-600 text-xs mt-0.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600">
        </div>

        <div class="pt-2">
            <label class="flex items-start gap-2 cursor-pointer">
                <input type="checkbox" name="persetujuan_privasi" value="1" {{ old('persetujuan_privasi') ? 'checked' : '' }}
                       class="mt-1 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                <span class="text-xs text-gray-600">
                    Saya menyetujui pemrosesan data pribadi saya sesuai ketentuan dalam
                    <a href="{{ route('privacy.policy') }}" target="_blank" class="text-emerald-700 underline font-medium">Kebijakan Privasi</a>.
                </span>
            </label>
            @error('persetujuan_privasi')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium py-2 rounded text-sm transition mt-2">
            Daftar Sebagai Nasabah
        </button>
    </form>

    <p class="mt-5 text-center text-xs text-gray-600">
        Sudah memiliki akun? <a href="{{ route('login') }}" class="text-emerald-700 font-semibold hover:underline">Masuk ke sini</a>
    </p>
</div>
@endsection
