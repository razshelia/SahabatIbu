@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div class="flex flex-wrap justify-between items-center gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Riwayat Setoran Sampah Saya</h1>
            <p class="text-xs text-gray-500">Catatan lengkap seluruh penimbangan dan tabungan sampah Anda (F-23)</p>
        </div>
        <a href="{{ route('nasabah.dashboard') }}" class="text-xs text-emerald-700 hover:underline">&larr; Kembali ke Dashboard</a>
    </div>

    <!-- Filter Tanggal -->
    <div class="bg-white p-3 rounded border border-gray-200">
        <form method="GET" action="{{ route('nasabah.setoran.index') }}" class="flex flex-wrap gap-2 items-center text-xs">
            <span class="font-semibold text-gray-600">Rentang Tanggal:</span>
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-2 py-1.5 border border-gray-300 rounded focus:border-emerald-600">
            <span>s/d</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-2 py-1.5 border border-gray-300 rounded focus:border-emerald-600">
            <button type="submit" class="bg-gray-800 hover:bg-black text-white px-3 py-1.5 rounded font-medium">Filter</button>
            @if($startDate || $endDate)
                <a href="{{ route('nasabah.setoran.index') }}" class="text-gray-500 hover:underline px-2">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel Riwayat Setoran -->
    <div class="bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
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
                    @forelse($transaksiList as $t)
                        <tr>
                            <td class="py-2.5 px-3 font-mono font-semibold text-gray-700">{{ $t->kode }}</td>
                            <td class="py-2.5 px-3 text-gray-500 whitespace-nowrap">{{ $t->tanggal->format('d/m/Y H:i') }}</td>
                            <td class="py-2.5 px-3 font-semibold">{{ number_format($t->total_berat, 2, ',', '.') }} kg</td>
                            <td class="py-2.5 px-3 font-bold text-emerald-700">Rp {{ number_format($t->total_nilai, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $t->status === 'berhasil' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $t->status }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-right">
                                <a href="{{ route('nasabah.setoran.show', $t->id) }}" class="text-blue-600 hover:underline">Rincian &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-400">Belum ada catatan setoran sampah pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-200">
            {{ $transaksiList->links() }}
        </div>
    </div>
</div>
@endsection
