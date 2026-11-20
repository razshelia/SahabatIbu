@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Rincian Setoran Sampah</h1>
            <p class="text-xs text-gray-500 font-mono">{{ $setoran->kode }}</p>
        </div>
        <a href="{{ route('nasabah.setoran.index') }}" class="text-xs text-emerald-700 hover:underline">&larr; Kembali</a>
    </div>

    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm space-y-4 text-xs">
        <div class="flex justify-between items-start pb-3 border-b border-gray-100">
            <div>
                <span class="text-[10px] uppercase font-bold text-gray-400">Status</span>
                <div class="mt-0.5">
                    <span class="px-2 py-0.5 rounded text-xs uppercase font-bold {{ $setoran->status === 'berhasil' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        {{ $setoran->status }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <span class="text-[10px] uppercase font-bold text-gray-400">Waktu Penimbangan</span>
                <p class="font-semibold text-gray-700">{{ $setoran->tanggal->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        @if($setoran->status === 'dibatalkan')
            <div class="bg-red-50 border border-red-200 text-red-800 p-3 rounded">
                <strong>Catatan Pembatalan:</strong><br>
                <span>Alasan: {{ $setoran->alasan_pembatalan }}</span>
            </div>
        @endif

        <div>
            <h3 class="font-bold text-gray-700 uppercase mb-2">Rincian Jenis Sampah</h3>
            <table class="w-full text-left border border-gray-200 rounded overflow-hidden">
                <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                    <tr>
                        <th class="p-2">Jenis Sampah</th>
                        <th class="p-2 text-right">Tarif</th>
                        <th class="p-2 text-right">Berat</th>
                        <th class="p-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($setoran->detailSetoran as $detail)
                        <tr>
                            <td class="p-2 font-medium">{{ $detail->jenisSampah->nama }}</td>
                            <td class="p-2 text-right font-mono text-gray-500">Rp {{ number_format($detail->harga_per_kg, 0, ',', '.') }}/{{ $detail->jenisSampah->satuan }}</td>
                            <td class="p-2 text-right font-mono">{{ number_format($detail->berat_kg, 2, ',', '.') }} {{ $detail->jenisSampah->satuan }}</td>
                            <td class="p-2 text-right font-mono font-bold text-emerald-700">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold border-t border-gray-200">
                    <tr>
                        <td colspan="2" class="p-2.5 text-right uppercase text-gray-600">Total:</td>
                        <td class="p-2.5 text-right font-mono">{{ number_format($setoran->total_berat, 2, ',', '.') }} kg</td>
                        <td class="p-2.5 text-right font-mono text-emerald-800 text-sm">Rp {{ number_format($setoran->total_nilai, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
