@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Pengurus</h1>
            <p class="text-sm text-gray-500">Ringkasan operasional Bank Sampah Sahabat Ibu</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.setoran.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold px-4 py-2 rounded shadow transition">
                + Input Setoran Sampah
            </a>
            <a href="{{ route('admin.nasabah.create') }}" class="bg-gray-800 hover:bg-black text-white text-xs font-semibold px-4 py-2 rounded shadow transition">
                + Tambah Nasabah
            </a>
        </div>
    </div>

    <!-- Metrik Utama -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold uppercase">Nasabah Aktif</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalNasabahAktif }}</p>
            <p class="text-xs text-amber-600 mt-1">
                <a href="{{ route('admin.nasabah.pendaftar') }}" class="hover:underline">
                    {{ $totalPendaftarMenunggu }} pendaftar menunggu
                </a>
            </p>
        </div>

        <div class="bg-white p-4 rounded border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold uppercase">Total Saldo Warga</p>
            <p class="text-2xl font-bold text-emerald-700 mt-1">Rp {{ number_format($totalSaldoTersimpan, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Ditahan: Rp {{ number_format($totalSaldoDitahan, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white p-4 rounded border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold uppercase">Total Sampah Terkumpul</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ number_format($totalBeratSukses, 2, ',', '.') }} kg</p>
            <p class="text-xs text-gray-400 mt-1">Total Setoran: Rp {{ number_format($totalSetoranSukses, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white p-4 rounded border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold uppercase">Pengajuan Penarikan</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $totalPenarikanMenunggu }}</p>
            <p class="text-xs text-amber-600 mt-1">
                <a href="{{ route('admin.penarikan.index', ['status' => 'menunggu']) }}" class="hover:underline">
                    Perlu diverifikasi &rarr;
                </a>
            </p>
        </div>
    </div>

    <!-- Rekap Sampah Per Jenis -->
    <div class="bg-white p-5 rounded border border-gray-200 shadow-sm">
        <h2 class="text-base font-bold text-gray-800 mb-3">Rekapitulasi Sampah Berdasarkan Jenis</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase border-b border-gray-200">
                    <tr>
                        <th class="py-2.5 px-3">Jenis Sampah</th>
                        <th class="py-2.5 px-3">Total Berat Terkumpul</th>
                        <th class="py-2.5 px-3">Total Nilai Tabungan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rekapSampah as $item)
                        <tr>
                            <td class="py-2.5 px-3 font-semibold">{{ $item->nama }}</td>
                            <td class="py-2.5 px-3">{{ number_format($item->total_berat, 2, ',', '.') }} {{ $item->satuan }}</td>
                            <td class="py-2.5 px-3 font-medium text-emerald-700">Rp {{ number_format($item->total_nilai, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-400">Belum ada transaksi setoran yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2 Kolom: Setoran Terbaru & Penarikan Terbaru -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Setoran Terbaru -->
        <div class="bg-white p-5 rounded border border-gray-200 shadow-sm">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-base font-bold text-gray-800">Setoran Sampah Terbaru</h2>
                <a href="{{ route('admin.setoran.index') }}" class="text-xs text-emerald-700 hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <tr>
                            <th class="py-2 px-2">Kode</th>
                            <th class="py-2 px-2">Nasabah</th>
                            <th class="py-2 px-2">Total Nilai</th>
                            <th class="py-2 px-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($setoranTerbaru as $setoran)
                            <tr>
                                <td class="py-2 px-2 font-mono text-gray-600">
                                    <a href="{{ route('admin.setoran.show', $setoran->id) }}" class="text-emerald-700 hover:underline">
                                        {{ $setoran->kode }}
                                    </a>
                                </td>
                                <td class="py-2 px-2 font-medium">{{ $setoran->nasabah->user->name }}</td>
                                <td class="py-2 px-2 text-emerald-700">Rp {{ number_format($setoran->total_nilai, 0, ',', '.') }}</td>
                                <td class="py-2 px-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $setoran->status === 'berhasil' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $setoran->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-3 text-center text-gray-400">Belum ada setoran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Penarikan Terbaru -->
        <div class="bg-white p-5 rounded border border-gray-200 shadow-sm">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-base font-bold text-gray-800">Pengajuan Penarikan Dana</h2>
                <a href="{{ route('admin.penarikan.index') }}" class="text-xs text-emerald-700 hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <tr>
                            <th class="py-2 px-2">Kode</th>
                            <th class="py-2 px-2">Nasabah</th>
                            <th class="py-2 px-2">Nominal</th>
                            <th class="py-2 px-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($penarikanTerbaru as $penarikan)
                            <tr>
                                <td class="py-2 px-2 font-mono text-gray-600">{{ $penarikan->kode }}</td>
                                <td class="py-2 px-2 font-medium">{{ $penarikan->nasabah->user->name }}</td>
                                <td class="py-2 px-2 font-semibold">Rp {{ number_format($penarikan->nominal, 0, ',', '.') }}</td>
                                <td class="py-2 px-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold
                                        @if($penarikan->status === 'menunggu') bg-amber-100 text-amber-800
                                        @elseif($penarikan->status === 'disetujui') bg-blue-100 text-blue-800
                                        @elseif($penarikan->status === 'dicairkan') bg-emerald-100 text-emerald-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $penarikan->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-3 text-center text-gray-400">Belum ada penarikan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
