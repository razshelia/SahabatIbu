@extends('layouts.guest')

@section('content')
<div class="bg-white p-6 rounded-lg shadow border border-gray-200">
    <h2 class="text-xl font-bold text-gray-800 mb-1 text-center">Lengkapi Profil Nasabah</h2>
    <p class="text-xs text-gray-500 mb-6 text-center">Halo, <strong>{{ $user->name }}</strong>! Silakan lengkapi kontak Anda untuk melanjutkan pendaftaran bank sampah.</p>

    <form method="POST" action="{{ route('profile.complete.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nomor Telepon / WhatsApp</label>
            <input type="text" name="telepon" value="{{ old('telepon') }}" placeholder="Contoh: 08123456789" required autofocus
                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('telepon') border-red-500 @enderror">
            @error('telepon') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Alamat Rumah</label>
            <textarea name="alamat" rows="2" required placeholder="Alamat rumah RT/RW di Perumahan Taman Gading"
                      class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('alamat') border-red-500 @enderror">{{ old('alamat') }}</textarea>
            @error('alamat') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
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

        <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium py-2 rounded text-sm transition">
            Simpan & Lanjutkan
        </button>
    </form>
</div>
@endsection
