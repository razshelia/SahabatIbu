@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Detail Transaksi Setoran</h1>
            <p class="text-xs text-gray-500 font-mono">{{ $setoran->kode }}</p>
        </div>
        <a href="{{ route('admin.setoran.index') }}" class="text-xs text-emerald-700 hover:underline">&larr; Kembali ke Daftar</a>
    </div>

    <!-- Kotak Utama Detail -->
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm space-y-4 text-xs">
        <div class="flex justify-between items-start pb-3 border-b border-gray-100">
            <div>
                <span class="text-[10px] uppercase font-bold text-gray-400">Status Transaksi</span>
                <div class="mt-0.5">
                    <span class="px-2.5 py-0.5 rounded text-xs uppercase font-bold {{ $setoran->status === 'berhasil' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        {{ $setoran->status }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <span class="text-[10px] uppercase font-bold text-gray-400">Tanggal Transaksi</span>
                <p class="font-semibold text-gray-700">{{ $setoran->tanggal->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 pb-3 border-b border-gray-100">
            <div>
                <span class="text-gray-400 font-semibold uppercase">Informasi Nasabah</span>
                <p class="font-bold text-gray-800 text-sm mt-0.5">{{ $setoran->nasabah->user->name }}</p>
                <p class="text-gray-500 font-mono">No: {{ $setoran->nasabah->nomor_nasabah }}</p>
                <p class="text-gray-500">{{ $setoran->nasabah->telepon }}</p>
            </div>
            <div>
                <span class="text-gray-400 font-semibold uppercase">Petugas Penerima</span>
                <p class="font-bold text-gray-800 text-sm mt-0.5">{{ $setoran->admin->name }}</p>
                <p class="text-gray-500">{{ $setoran->admin->email }}</p>
            </div>
        </div>

        @if($setoran->status === 'dibatalkan')
            <div class="bg-red-50 border border-red-200 text-red-800 p-3 rounded">
                <strong>Informasi Pembatalan:</strong><br>
                <span>Dibatalkan oleh: {{ $setoran->dibatalkanOleh?->name ?? 'Admin' }} pada {{ $setoran->dibatalkan_pada?->format('d/m/Y H:i') }}</span><br>
                <span>Alasan: {{ $setoran->alasan_pembatalan }}</span>
            </div>
        @endif

        <!-- Rincian Item (Snapshot Harga BR-04) -->
        <div>
            <h3 class="font-bold text-gray-700 uppercase mb-2">Rincian Sampah Disetor</h3>
            <table class="w-full text-left border border-gray-200 rounded overflow-hidden">
                <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                    <tr>
                        <th class="p-2">Jenis Sampah</th>
                        <th class="p-2 text-right">Harga Snapshot</th>
                        <th class="p-2 text-right">Berat</th>
                        <th class="p-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($setoran->detailSetoran as $detail)
                        <tr>
                            <td class="p-2 font-medium">{{ $detail->jenisSampah->nama }}</td>
                            <td class="p-2 text-right font-mono text-gray-600">Rp {{ number_format($detail->harga_per_kg, 0, ',', '.') }}/{{ $detail->jenisSampah->satuan }}</td>
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

        <!-- Tombol Batalkan Transaksi (F-20, BR-10, BR-17) -->
        @if($setoran->status === 'berhasil')
            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="button" onclick="document.getElementById('modal-batal-setoran').classList.remove('hidden')"
                        class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded text-xs transition">
                    Batalkan Transaksi Ini
                </button>
            </div>

            <!-- Modal Pembatalan Setoran -->
            <div id="modal-batal-setoran" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden text-left">
                <div class="bg-white rounded-lg p-5 max-w-sm w-full space-y-3">
                    <h3 class="font-bold text-gray-800 text-sm">Batalkan Transaksi Setoran</h3>
                    <p class="text-[11px] text-gray-500">
                        Pembatalan akan mengurangi saldo nasabah sebesar <strong>Rp {{ number_format($setoran->total_nilai, 0, ',', '.') }}</strong>. Sesuai aturan BR-17, pembatalan akan ditolak jika saldo tersedia nasabah tidak mencukupi.
                    </p>
                    <form method="POST" action="{{ route('admin.setoran.batalkan', $setoran->id) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">Alasan Pembatalan (Wajib):</label>
                            <textarea name="alasan_pembatalan" rows="3" required placeholder="Contoh: Kesalahan penimbangan berat jenis kertas"
                                      class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs"></textarea>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" onclick="document.getElementById('modal-batal-setoran').classList.add('hidden')"
                                    class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-xs">Tutup</button>
                            <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-semibold">
                                Konfirmasi Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
