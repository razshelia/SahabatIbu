@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Laporan Operasional & Keuangan</h1>
            <p class="text-xs text-gray-500">Rekapitulasi setoran sampah dan penarikan saldo per periode (F-31, F-32)</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.laporan.export.setoran', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-2 rounded transition">
                &darr; Ekspor CSV Setoran
            </a>
            <a href="{{ route('admin.laporan.export.penarikan', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-3 py-2 rounded transition">
                &darr; Ekspor CSV Penarikan
            </a>
            <a href="{{ route('admin.laporan.cetak', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
               class="bg-gray-800 hover:bg-black text-white text-xs font-semibold px-3 py-2 rounded transition">
                &#128438; Cetak Halaman
            </a>
        </div>
    </div>

    <!-- Filter Rentang Periode -->
    <div class="bg-white p-3 rounded border border-gray-200">
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-wrap gap-3 items-center text-xs">
            <div class="flex items-center gap-1.5">
                <span class="font-semibold text-gray-600">Dari Tanggal:</span>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-2 py-1.5 border border-gray-300 rounded focus:border-emerald-600">
            </div>
            <div class="flex items-center gap-1.5">
                <span class="font-semibold text-gray-600">Sampai:</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-2 py-1.5 border border-gray-300 rounded focus:border-emerald-600">
            </div>
            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-1.5 rounded font-medium">Terapkan Filter</button>
        </form>
    </div>

    <!-- Kartu Rekapitulasi Periode -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold uppercase">Total Tabungan Masuk</p>
            <p class="text-xl font-bold text-emerald-700 mt-1">Rp {{ number_format($rekap['total_setoran'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">{{ $rekap['jumlah_setoran'] }} transaksi setoran</p>
        </div>

        <div class="bg-white p-4 rounded border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold uppercase">Total Sampah Terkumpul</p>
            <p class="text-xl font-bold text-blue-700 mt-1">{{ number_format($rekap['total_berat'], 2, ',', '.') }} kg</p>
            <p class="text-[11px] text-gray-400 mt-0.5">Seluruh jenis sampah</p>
        </div>

        <div class="bg-white p-4 rounded border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold uppercase">Total Penarikan Dicairkan</p>
            <p class="text-xl font-bold text-red-600 mt-1">Rp {{ number_format($rekap['total_penarikan'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">{{ $rekap['jumlah_penarikan'] }} pencairan tunai</p>
        </div>

        <div class="bg-white p-4 rounded border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold uppercase">Selisih Kas Bersih</p>
            <p class="text-xl font-bold text-gray-800 mt-1">Rp {{ number_format($rekap['saldo_bersih'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">Setoran minus Penarikan</p>
        </div>
    </div>

    <!-- Rekap Sampah Per Jenis -->
    <div class="bg-white p-4 rounded border border-gray-200 shadow-sm">
        <h2 class="font-bold text-sm text-gray-800 mb-2">Rekapitulasi Sampah Berdasarkan Jenis</h2>
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-600 uppercase border-b border-gray-200">
                <tr>
                    <th class="py-2 px-3">Jenis Sampah</th>
                    <th class="py-2 px-3">Total Berat (kg)</th>
                    <th class="py-2 px-3">Total Nilai Tabungan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rekapJenis as $j)
                    <tr>
                        <td class="py-2 px-3 font-semibold">{{ $j->jenis_nama }}</td>
                        <td class="py-2 px-3">{{ number_format($j->total_berat, 2, ',', '.') }} {{ $j->jenis_satuan }}</td>
                        <td class="py-2 px-3 font-bold text-emerald-700">Rp {{ number_format($j->total_nilai, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-3 text-center text-gray-400">Tidak ada data sampah pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
