@extends('layouts.guest')

@section('content')
<div class="bg-white p-6 rounded-lg shadow border border-gray-200 text-center">
    @if($user->status === 'menunggu')
        <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
            ⏳
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Menunggu Verifikasi Pengurus</h2>
        <p class="text-sm text-gray-600 mb-4">
            Terima kasih telah mendaftar, <strong>{{ $user->name }}</strong>.<br>
            Pendaftaran nasabah Anda saat ini sedang diperiksa dan diverifikasi oleh pengurus Bank Sampah Sahabat Ibu.
        </p>
        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-xs p-3 rounded mb-6 text-left">
            <strong>Catatan:</strong> Akun Anda akan aktif setelah pengurus menyetujui pendaftaran. Silakan cek berkala atau hubungi pengurus RT/RW Bank Sampah Sahabat Ibu.
        </div>
    @elseif($user->status === 'ditolak')
        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
            ✕
        </div>
        <h2 class="text-xl font-bold text-red-700 mb-2">Pendaftaran Ditolak</h2>
        <p class="text-sm text-gray-600 mb-4">
            Mohon maaf, pendaftaran nasabah Anda tidak dapat disetujui oleh pengurus bank sampah.
        </p>
        @if($user->alasan_penolakan)
            <div class="bg-red-50 border border-red-200 text-red-800 text-xs p-3 rounded mb-6 text-left">
                <strong>Alasan Penolakan:</strong><br>
                {{ $user->alasan_penolakan }}
            </div>
        @endif
    @endif

    <div class="border-t border-gray-200 pt-4 flex justify-center gap-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-semibold px-4 py-2 rounded transition">
                Keluar dari Akun
            </button>
        </form>
    </div>
</div>
@endsection
