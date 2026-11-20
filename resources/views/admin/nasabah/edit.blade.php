@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow border border-gray-200">
    <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
        <div>
            <h1 class="text-lg font-bold text-gray-800">Ubah Data Nasabah</h1>
            <p class="text-xs text-gray-500">Nomor: {{ $nasabah->nomor_nasabah }}</p>
        </div>
        <a href="{{ route('admin.nasabah.index') }}" class="text-xs text-gray-500 hover:underline">&larr; Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.nasabah.update', $nasabah->id) }}" class="space-y-4 text-xs">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-semibold text-gray-700 uppercase mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $nasabah->user->name) }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('name') border-red-500 @enderror">
            @error('name') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block font-semibold text-gray-700 uppercase mb-1">Alamat Email (Tidak Dapat Diubah)</label>
            <input type="email" value="{{ $nasabah->user->email }}" disabled
                   class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded text-sm text-gray-500 cursor-not-allowed">
        </div>

        <div>
            <label class="block font-semibold text-gray-700 uppercase mb-1">Nomor Telepon / WhatsApp</label>
            <input type="text" name="telepon" value="{{ old('telepon', $nasabah->telepon) }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('telepon') border-red-500 @enderror">
            @error('telepon') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block font-semibold text-gray-700 uppercase mb-1">Alamat Rumah</label>
            <textarea name="alamat" rows="3" required
                      class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-emerald-600 @error('alamat') border-red-500 @enderror">{{ old('alamat', $nasabah->alamat) }}</textarea>
            @error('alamat') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-2 rounded text-sm transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
