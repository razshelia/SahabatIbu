@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Halo, {{ $user->name }}!</h1>
            <p class="text-xs text-gray-500">Nomor Nasabah: <span class="font-mono font-bold text-gray-700">{{ $nasabah->nomor_nasabah }}</span></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('nasabah.penarikan.index') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold px-4 py-2 rounded shadow transition">
                Tarik Saldo Tunai
            </a>
            <a href="{{ route('nasabah.katalog.index') }}" class="bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 text-xs font-semibold px-3 py-2 rounded transition">
                Lihat Harga Sampah
            </a>
        </div>
    </div>

    <!-- Ringkasan Saldo Tabungan (F-22, BR-06, BR-07) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Saldo Tersedia -->
        <div class="bg-emerald-700 text-white p-5 rounded-lg shadow-sm">
            <span class="text-xs uppercase tracking-wider text-emerald-200 font-semibold">Saldo Siap Ditarik (Tersedia)</span>
            <p class="text-3xl font-extrabold mt-1">Rp {{ number_format($nasabah->saldo_tersedia, 0, ',', '.') }}</p>
            <p class="text-xs text-emerald-100 mt-2">Dapat ditarik tunai kapan saja di jadwal operasional.</p>
        </div>

        <!-- Saldo Ditahan -->
        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
            <span class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Saldo Sedang Ditahan</span>
            <p class="text-2xl font-bold text-amber-600 mt-1">Rp {{ number_format($nasabah->saldo_ditahan, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-2">Dana yang sedang dalam proses pengajuan penarikan.</p>
        </div>

        <!-- Total Saldo & Sampah -->
        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
            <span class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Total Sampah Disetor</span>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ number_format($totalBeratSetoran, 2, ',', '.') }} kg</p>
            <p class="text-xs text-gray-500 mt-2">Dari total <strong>{{ $jumlahTransaksiSetoran }}</strong> kali penimbangan sampah.</p>
        </div>
    </div>

    <!-- Peringatan Pengajuan Penarikan Aktif -->
    @if($penarikanAktif->isNotEmpty())
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded shadow-sm text-xs">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-amber-800">Anda Memiliki Pengajuan Penarikan Aktif</h3>
                    <p class="text-amber-700 mt-0.5">
                        Kode: <span class="font-mono font-bold">{{ $penarikanAktif->first()->kode }}</span> &bull;
                        Nominal: <strong>Rp {{ number_format($penarikanAktif->first()->nominal, 0, ',', '.') }}</strong> &bull;
                        Status: <span class="uppercase font-bold">{{ $penarikanAktif->first()->status }}</span>
                    </p>
                </div>
                <a href="{{ route('nasabah.penarikan.index') }}" class="text-amber-900 font-bold underline">Lihat Rincian &rarr;</a>
            </div>
        </div>
    @endif

    <!-- 5 Setoran Terakhir Nasabah -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="font-bold text-sm text-gray-800">Riwayat Setoran Sampah Terakhir Anda</h2>
            <a href="{{ route('nasabah.setoran.index') }}" class="text-xs text-emerald-700 hover:underline">Lihat Semua Riwayat &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase border-b border-gray-200">
                    <tr>
                        <th class="py-2.5 px-3">Kode Transaksi</th>
                        <th class="py-2.5 px-3">Tanggal</th>
                        <th class="py-2.5 px-3">Total Berat</th>
                        <th class="py-2.5 px-3">Nilai Tabungan</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3 text-right">Rincian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($setoranTerbaru as $setoran)
                        <tr>
                            <td class="py-2.5 px-3 font-mono font-semibold text-gray-700">{{ $setoran->kode }}</td>
                            <td class="py-2.5 px-3 text-gray-500 whitespace-nowrap">{{ $setoran->tanggal->format('d/m/Y H:i') }}</td>
                            <td class="py-2.5 px-3 font-semibold">{{ number_format($setoran->total_berat, 2, ',', '.') }} kg</td>
                            <td class="py-2.5 px-3 font-bold text-emerald-700">Rp {{ number_format($setoran->total_nilai, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $setoran->status === 'berhasil' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $setoran->status }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-right">
                                <a href="{{ route('nasabah.setoran.show', $setoran->id) }}" class="text-blue-600 hover:underline">Rincian &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-400">Anda belum pernah melakukan setoran sampah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
