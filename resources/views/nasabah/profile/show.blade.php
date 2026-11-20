@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Profil Saya</h1>
            <p class="text-xs text-gray-500">Kelola informasi kontak dan pengaturan keamanan akun Anda (F-11)</p>
        </div>
        <a href="{{ route('nasabah.dashboard') }}" class="text-xs text-emerald-700 hover:underline">&larr; Kembali ke Dashboard</a>
    </div>

    <!-- Ringkasan Akun -->
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm text-xs grid grid-cols-2 gap-4">
        <div>
            <span class="text-gray-400 font-semibold uppercase">Nomor Nasabah</span>
            <p class="font-mono font-bold text-gray-800 text-sm mt-0.5">{{ $nasabah->nomor_nasabah }}</p>
        </div>
        <div>
            <span class="text-gray-400 font-semibold uppercase">Status Keaktifan</span>
            <p class="font-bold text-emerald-700 text-sm mt-0.5 uppercase">{{ $user->status }}</p>
        </div>
        <div>
            <span class="text-gray-400 font-semibold uppercase">Nama Lengkap</span>
            <p class="font-bold text-gray-800 mt-0.5">{{ $user->name }}</p>
        </div>
        <div>
            <span class="text-gray-400 font-semibold uppercase">Alamat Email</span>
            <p class="font-bold text-gray-800 mt-0.5">{{ $user->email }}</p>
        </div>
    </div>

    <!-- Form Ubah Kontak (F-11) -->
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm space-y-4 text-xs">
        <h2 class="font-bold text-sm text-gray-800 border-b border-gray-100 pb-2">Perbarui Data Kontak Rumah</h2>

        <form method="POST" action="{{ route('nasabah.profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-semibold text-gray-700 uppercase mb-1">Nomor Telepon / WhatsApp</label>
                <input type="text" name="telepon" value="{{ old('telepon', $nasabah->telepon) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:border-emerald-600 @error('telepon') border-red-500 @enderror">
                @error('telepon') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-semibold text-gray-700 uppercase mb-1">Alamat Tempat Tinggal</label>
                <textarea name="alamat" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:border-emerald-600 @error('alamat') border-red-500 @enderror">{{ old('alamat', $nasabah->alamat) }}</textarea>
                @error('alamat') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-semibold px-4 py-2 rounded text-xs transition">
                Simpan Perubahan Kontak
            </button>
        </form>
    </div>

    <!-- Ganti Kata Sandi -->
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm text-xs flex justify-between items-center">
        <div>
            <h3 class="font-bold text-gray-800">Kata Sandi Akun</h3>
            <p class="text-gray-500 text-[11px]">Amankan akun Anda dengan memperbarui kata sandi secara berkala.</p>
        </div>
        <a href="{{ route('password.change.form') }}" class="bg-gray-800 hover:bg-black text-white text-xs font-semibold px-3 py-2 rounded transition">
            Ganti Kata Sandi
        </a>
    </div>
</div>
@endsection
