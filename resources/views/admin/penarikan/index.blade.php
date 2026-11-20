@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div class="flex flex-wrap justify-between items-center gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Verifikasi & Pencairan Dana Tunai</h1>
            <p class="text-xs text-gray-500">Kelola permohonan penarikan saldo nasabah dan konfirmasi penyerahan tunai</p>
        </div>
    </div>

    <!-- Filter Status -->
    <div class="bg-white p-3 rounded border border-gray-200">
        <form method="GET" action="{{ route('admin.penarikan.index') }}" class="flex flex-wrap gap-2 items-center text-xs">
            <span class="font-semibold text-gray-600">Status Penarikan:</span>
            <select name="status" class="px-3 py-1.5 border border-gray-300 rounded focus:border-emerald-600">
                <option value="">-- Semua Status --</option>
                <option value="menunggu" {{ $status === 'menunggu' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui (Siap Dicairkan)</option>
                <option value="dicairkan" {{ $status === 'dicairkan' ? 'selected' : '' }}>Sudah Dicairkan</option>
                <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                <option value="dibatalkan" {{ $status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button type="submit" class="bg-gray-800 hover:bg-black text-white px-3 py-1.5 rounded font-medium">Filter</button>
            @if($status)
                <a href="{{ route('admin.penarikan.index') }}" class="text-gray-500 hover:underline px-2">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel Penarikan -->
    <div class="bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase border-b border-gray-200">
                    <tr>
                        <th class="py-2.5 px-3">Kode</th>
                        <th class="py-2.5 px-3">Waktu Pengajuan</th>
                        <th class="py-2.5 px-3">Nasabah</th>
                        <th class="py-2.5 px-3">Nominal Tarik</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3 text-right">Aksi & Pemrosesan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($penarikanList as $p)
                        <tr>
                            <td class="py-2.5 px-3 font-mono font-bold text-gray-700">{{ $p->kode }}</td>
                            <td class="py-2.5 px-3 text-gray-500 whitespace-nowrap">{{ $p->diajukan_pada->format('d/m/Y H:i') }}</td>
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-gray-800">{{ $p->nasabah->user->name }}</div>
                                <div class="text-[11px] text-gray-400 font-mono">{{ $p->nasabah->nomor_nasabah }} &bull; {{ $p->nasabah->telepon }}</div>
                            </td>
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
                                    <div class="text-[10px] text-red-600 mt-0.5 italic">Alasan: {{ $p->alasan_penolakan }}</div>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-right space-x-2 whitespace-nowrap">
                                @if($p->status === 'menunggu')
                                    <!-- Setujui -->
                                    <form method="POST" action="{{ route('admin.penarikan.setujui', $p->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-2.5 py-1 rounded text-xs"
                                                onclick="return confirm('Setujui pengajuan penarikan dana ini?')">
                                            Setujui
                                        </button>
                                    </form>

                                    <!-- Tolak -->
                                    <button type="button" onclick="document.getElementById('modal-tolak-penarikan-{{ $p->id }}').classList.remove('hidden')"
                                            class="bg-red-600 hover:bg-red-700 text-white font-medium px-2.5 py-1 rounded text-xs">
                                        Tolak
                                    </button>

                                    <!-- Modal Tolak Penarikan -->
                                    <div id="modal-tolak-penarikan-{{ $p->id }}" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden text-left whitespace-normal">
                                        <div class="bg-white rounded-lg p-5 max-w-sm w-full space-y-3">
                                            <h3 class="font-bold text-gray-800 text-sm">Tolak Pengajuan {{ $p->kode }}</h3>
                                            <form method="POST" action="{{ route('admin.penarikan.tolak', $p->id) }}" class="space-y-3">
                                                @csrf
                                                <div>
                                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Alasan Penolakan (Wajib):</label>
                                                    <textarea name="alasan_penolakan" rows="3" required placeholder="Contoh: Kas tunai belum mencukupi hari ini"
                                                              class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs"></textarea>
                                                </div>
                                                <div class="flex justify-end gap-2 pt-2">
                                                    <button type="button" onclick="document.getElementById('modal-tolak-penarikan-{{ $p->id }}').classList.add('hidden')"
                                                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-xs">Batal</button>
                                                    <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-semibold">
                                                        Kirim Penolakan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @elseif($p->status === 'disetujui')
                                    <!-- Konfirmasi Pencairan Tunai (F-30) -->
                                    <form method="POST" action="{{ route('admin.penarikan.cairkan', $p->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1 rounded text-xs shadow-sm"
                                                onclick="return confirm('Konfirmasi bahwa uang tunai sebesar Rp {{ number_format($p->nominal, 0, ',', '.') }} telah diserahkan kepada nasabah?')">
                                            Konfirmasi Cairkan Tunai
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 italic">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-400">Tidak ada pengajuan penarikan dana.</td>
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
@endsection
