@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Penarikan Saldo Tabungan</h1>
            <p class="text-xs text-gray-500">Ajukan pencairan saldo tabungan sampah Anda menjadi uang tunai (F-25, F-28)</p>
        </div>
        <a href="{{ route('nasabah.dashboard') }}" class="text-xs text-emerald-700 hover:underline">&larr; Kembali ke Dashboard</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Kolom 1: Formulir Ajukan Penarikan -->
        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm space-y-4 text-xs">
            <h2 class="font-bold text-sm text-gray-800">Formulir Pengajuan Penarikan</h2>

            <div class="bg-emerald-50 border border-emerald-200 p-3 rounded">
                <span class="text-[11px] text-emerald-800 font-semibold uppercase">Saldo Tersedia (Siap Ditarik):</span>
                <p class="text-xl font-extrabold text-emerald-800 mt-0.5">
                    Rp {{ number_format($nasabah->saldo_tersedia, 0, ',', '.') }}
                </p>
                @if($nasabah->saldo_ditahan > 0)
                    <p class="text-[10px] text-amber-700 mt-1">
                        (Sedang ditahan: Rp {{ number_format($nasabah->saldo_ditahan, 0, ',', '.') }})
                    </p>
                @endif
            </div>

            <form method="POST" action="{{ route('nasabah.penarikan.store') }}" class="space-y-3">
                @csrf

                <div>
                    <label class="block font-semibold text-gray-700 uppercase mb-1">Nominal yang Ingin Ditarik (Rp)</label>
                    <input type="number" step="1000" min="1000" max="{{ $nasabah->saldo_tersedia }}" name="nominal"
                           placeholder="Contoh: 50000" required
                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:border-emerald-600 font-mono @error('nominal') border-red-500 @enderror">
                    @error('nominal') <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
                    <p class="text-[11px] text-gray-400 mt-1">Maksimal penarikan: Rp {{ number_format($nasabah->saldo_tersedia, 0, ',', '.') }}</p>
                </div>

                <div class="bg-gray-50 p-2.5 rounded text-[11px] text-gray-500 leading-relaxed">
                    <strong>Catatan:</strong> Setelah diajukan, saldo sebesar nominal tersebut akan <em>ditahan sementara</em>. Pencairan uang tunai dilakukan secara tatap muka dengan pengurus bank sampah.
                </div>

                <button type="submit"
                        {{ $nasabah->saldo_tersedia <= 0 ? 'disabled' : '' }}
                        class="w-full bg-emerald-700 hover:bg-emerald-800 disabled:bg-gray-300 text-white font-bold py-2 rounded text-xs transition">
                    Ajukan Penarikan Sekarang
                </button>
            </form>
        </div>

        <!-- Kolom 2 & 3: Riwayat Penarikan Dana -->
        <div class="md:col-span-2 bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
            <div class="p-4 border-b border-gray-100">
                <h2 class="font-bold text-sm text-gray-800">Riwayat Pengajuan Penarikan Dana</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 uppercase border-b border-gray-200">
                        <tr>
                            <th class="py-2.5 px-3">Kode</th>
                            <th class="py-2.5 px-3">Waktu Pengajuan</th>
                            <th class="py-2.5 px-3">Nominal</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($penarikanList as $p)
                            <tr>
                                <td class="py-2.5 px-3 font-mono font-bold text-gray-700">{{ $p->kode }}</td>
                                <td class="py-2.5 px-3 text-gray-500 whitespace-nowrap">{{ $p->diajukan_pada->format('d/m/Y H:i') }}</td>
                                <td class="py-2.5 px-3 font-bold text-emerald-700">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold
                                        @if($p->status === 'menunggu') bg-amber-100 text-amber-800
                                        @elseif($p->status === 'disetujui') bg-blue-100 text-blue-800
                                        @elseif($p->status === 'dicairkan') bg-emerald-100 text-emerald-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $p->status }}
                                    </span>
                                    @if($p->alasan_penolakan)
                                        <div class="text-[10px] text-red-600 italic mt-0.5">Alasan: {{ $p->alasan_penolakan }}</div>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                    @if($p->status === 'menunggu')
                                        <!-- Tombol Batalkan Pengajuan Nasabah (F-27, BR-09) -->
                                        <form method="POST" action="{{ route('nasabah.penarikan.batalkan', $p->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:underline font-semibold"
                                                    onclick="return confirm('Batalkan pengajuan penarikan dana ini?')">
                                                Batalkan
                                            </button>
                                        </form>
                                    @elseif($p->status === 'disetujui')
                                        <span class="text-blue-700 font-medium">Siap Ambil Tunai</span>
                                    @elseif($p->status === 'dicairkan')
                                        <span class="text-emerald-700">Uang Diterima</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-400">Belum ada catatan pengajuan penarikan dana.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-t border-gray-200">
                {{ $penarikanList->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
