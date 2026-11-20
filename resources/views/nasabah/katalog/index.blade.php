@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Katalog Jenis & Harga Sampah</h1>
            <p class="text-xs text-gray-500">Tarif harga tabungan sampah yang berlaku saat ini (F-15)</p>
        </div>
        <a href="{{ route('nasabah.dashboard') }}" class="text-xs text-emerald-700 hover:underline">&larr; Kembali ke Dashboard</a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4">Jenis Sampah</th>
                        <th class="py-3 px-4">Satuan</th>
                        <th class="py-3 px-4">Harga Tabungan</th>
                        <th class="py-3 px-4">Berlaku Sejak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($katalog as $item)
                        <tr>
                            <td class="py-3 px-4 font-bold text-gray-800 text-sm">{{ $item->nama }}</td>
                            <td class="py-3 px-4">{{ $item->satuan }}</td>
                            <td class="py-3 px-4 font-extrabold text-emerald-700 text-sm">
                                @if($item->harga_berlaku)
                                    Rp {{ number_format($item->harga_berlaku->harga_per_kg, 0, ',', '.') }} / {{ $item->satuan }}
                                @else
                                    <span class="text-gray-400 font-normal">Belum tersedia</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-500">
                                {{ $item->harga_berlaku?->berlaku_mulai->format('d F Y') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-400">Belum ada jenis sampah yang aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded p-4 text-xs text-blue-900 leading-relaxed">
        <strong>Ketentuan Penyetoran Sampah (BR-01, BR-02, BR-05):</strong><br>
        1. Sampah yang disetorkan harus sudah dipilah dalam kondisi bersih dan kering.<br>
        2. Penimbangan berat diukur dalam satuan kilogram (kg) dengan pembulatan hingga 2 desimal.<br>
        3. Tabungan akan langsung bertambah ke akun Anda secara otomatis setelah penimbangan dicatat oleh pengurus.
    </div>
</div>
@endsection
