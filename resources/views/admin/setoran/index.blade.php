@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div class="flex flex-wrap justify-between items-center gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Transaksi Setoran Sampah</h1>
            <p class="text-xs text-gray-500">Daftar rekaman penimbangan dan setoran sampah nasabah</p>
        </div>
        <a href="{{ route('admin.setoran.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold px-4 py-2 rounded shadow transition">
            + Input Setoran Baru
        </a>
    </div>

    <!-- Filter Form (F-21) -->
    <div class="bg-white p-3 rounded border border-gray-200">
        <form method="GET" action="{{ route('admin.setoran.index') }}" class="flex flex-wrap gap-2 items-center text-xs">
            <select name="nasabah_id" class="px-2 py-1.5 border border-gray-300 rounded focus:border-emerald-600">
                <option value="">-- Semua Nasabah --</option>
                @foreach($nasabahList as $n)
                    <option value="{{ $n->id }}" {{ $nasabahId == $n->id ? 'selected' : '' }}>
                        {{ $n->nomor_nasabah }} - {{ $n->user->name }}
                    </option>
                @endforeach
            </select>

            <div class="flex items-center gap-1">
                <span>Dari:</span>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-2 py-1.5 border border-gray-300 rounded">
            </div>

            <div class="flex items-center gap-1">
                <span>Sampai:</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-2 py-1.5 border border-gray-300 rounded">
            </div>

            <select name="status" class="px-2 py-1.5 border border-gray-300 rounded">
                <option value="">-- Semua Status --</option>
                <option value="berhasil" {{ $status === 'berhasil' ? 'selected' : '' }}>Berhasil</option>
                <option value="dibatalkan" {{ $status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>

            <button type="submit" class="bg-gray-800 hover:bg-black text-white px-3 py-1.5 rounded font-medium">Filter</button>
            @if($nasabahId || $startDate || $endDate || $status)
                <a href="{{ route('admin.setoran.index') }}" class="text-gray-500 hover:underline px-2">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel Setoran -->
    <div class="bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase border-b border-gray-200">
                    <tr>
                        <th class="py-2.5 px-3">Kode</th>
                        <th class="py-2.5 px-3">Tanggal</th>
                        <th class="py-2.5 px-3">Nasabah</th>
                        <th class="py-2.5 px-3">Total Berat</th>
                        <th class="py-2.5 px-3">Total Tabungan</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transaksiList as $t)
                        <tr>
                            <td class="py-2.5 px-3 font-mono font-bold text-gray-700">
                                <a href="{{ route('admin.setoran.show', $t->id) }}" class="text-emerald-700 hover:underline">
                                    {{ $t->kode }}
                                </a>
                            </td>
                            <td class="py-2.5 px-3 text-gray-500 whitespace-nowrap">{{ $t->tanggal->format('d/m/Y H:i') }}</td>
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-gray-800">{{ $t->nasabah->user->name }}</div>
                                <div class="text-[11px] text-gray-400 font-mono">{{ $t->nasabah->nomor_nasabah }}</div>
                            </td>
                            <td class="py-2.5 px-3 font-semibold">{{ number_format($t->total_berat, 2, ',', '.') }} kg</td>
                            <td class="py-2.5 px-3 font-bold text-emerald-700">Rp {{ number_format($t->total_nilai, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $t->status === 'berhasil' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $t->status }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-right">
                                <a href="{{ route('admin.setoran.show', $t->id) }}" class="text-blue-600 hover:underline">Rincian &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-400">Tidak ada data setoran ditemukan.</td>
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
