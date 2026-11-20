@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div class="flex flex-wrap justify-between items-center gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Data Nasabah</h1>
            <p class="text-xs text-gray-500">Kelola informasi nasabah dan status keaktifan tabungan</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.nasabah.pendaftar') }}" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-3 py-2 rounded transition">
                Lihat Pendaftar Baru
            </a>
            <a href="{{ route('admin.nasabah.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold px-3 py-2 rounded transition">
                + Tambah Nasabah Langsung
            </a>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-3 rounded border border-gray-200">
        <form method="GET" action="{{ route('admin.nasabah.index') }}" class="flex flex-wrap gap-2 items-center text-xs">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, nomor, telepon, alamat..."
                   class="px-3 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-emerald-600 flex-grow max-w-sm">

            <select name="status" class="px-3 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-emerald-600">
                <option value="">-- Semua Status --</option>
                <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ $status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                <option value="menunggu" {{ $status === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <button type="submit" class="bg-gray-800 hover:bg-black text-white px-3 py-1.5 rounded font-medium">Filter</button>
            @if($search || $status)
                <a href="{{ route('admin.nasabah.index') }}" class="text-gray-500 hover:underline px-2">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel Nasabah -->
    <div class="bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase border-b border-gray-200">
                    <tr>
                        <th class="py-2.5 px-3">No. Nasabah</th>
                        <th class="py-2.5 px-3">Nama Lengkap</th>
                        <th class="py-2.5 px-3">Kontak & Alamat</th>
                        <th class="py-2.5 px-3">Saldo Tabungan</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($nasabahList as $nasabah)
                        <tr>
                            <td class="py-2.5 px-3 font-mono font-semibold text-gray-700">{{ $nasabah->nomor_nasabah }}</td>
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-gray-800">{{ $nasabah->user->name }}</div>
                                <div class="text-[11px] text-gray-400">{{ $nasabah->user->email }}</div>
                            </td>
                            <td class="py-2.5 px-3">
                                <div>{{ $nasabah->telepon }}</div>
                                <div class="text-[11px] text-gray-400 truncate max-w-xs">{{ $nasabah->alamat }}</div>
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-emerald-700">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</div>
                                @if($nasabah->saldo_ditahan > 0)
                                    <div class="text-[10px] text-amber-600">Ditahan: Rp {{ number_format($nasabah->saldo_ditahan, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold
                                    @if($nasabah->user->status === 'aktif') bg-emerald-100 text-emerald-800
                                    @elseif($nasabah->user->status === 'nonaktif') bg-gray-200 text-gray-700
                                    @elseif($nasabah->user->status === 'menunggu') bg-amber-100 text-amber-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $nasabah->user->status }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-right space-x-1">
                                <a href="{{ route('admin.nasabah.edit', $nasabah->id) }}" class="text-blue-600 hover:underline">Ubah</a>

                                @if(in_array($nasabah->user->status, ['aktif', 'nonaktif']))
                                    <form method="POST" action="{{ route('admin.nasabah.toggle-status', $nasabah->user->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="{{ $nasabah->user->status === 'aktif' ? 'text-amber-600' : 'text-emerald-600' }} hover:underline"
                                                onclick="return confirm('Yakin ingin mengubah status akun nasabah ini?')">
                                            {{ $nasabah->user->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                @endif

                                @if($nasabah->user->status === 'nonaktif' && $nasabah->saldo == 0 && $nasabah->saldo_ditahan == 0)
                                    <button type="button" onclick="document.getElementById('modal-anonim-{{ $nasabah->id }}').classList.remove('hidden')"
                                            class="text-red-600 hover:underline">
                                        Anonimkan
                                    </button>

                                    <!-- Modal Konfirmasi Anonimisasi (F-43, BR-18) -->
                                    <div id="modal-anonim-{{ $nasabah->id }}" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden text-left">
                                        <div class="bg-white rounded-lg p-5 max-w-sm w-full space-y-3">
                                            <h3 class="font-bold text-red-700 text-sm">Konfirmasi Anonimisasi Data</h3>
                                            <p class="text-xs text-gray-600">
                                                Tindakan ini akan <strong>menghapus data pribadi</strong> milik nasabah <strong>{{ $nasabah->user->name }}</strong> ({{ $nasabah->nomor_nasabah }}) secara permanen sesuai BR-18. Nama, email, telepon, dan alamat akan disamarkan.
                                            </p>
                                            <form method="POST" action="{{ route('admin.nasabah.anonimkan', $nasabah->id) }}" class="space-y-3">
                                                @csrf
                                                <div>
                                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Ketik kata <strong>ANONIMKAN</strong>:</label>
                                                    <input type="text" name="konfirmasi" required placeholder="ANONIMKAN"
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                                                </div>
                                                <div class="flex justify-end gap-2 pt-2">
                                                    <button type="button" onclick="document.getElementById('modal-anonim-{{ $nasabah->id }}').classList.add('hidden')"
                                                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-xs">Batal</button>
                                                    <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-semibold">
                                                        Lanjutkan Anonimisasi
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-400">Tidak ada data nasabah ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-200">
            {{ $nasabahList->links() }}
        </div>
    </div>
</div>
@endsection
