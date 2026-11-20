@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Katalog Jenis & Harga Sampah</h1>
        <p class="text-xs text-gray-500">Kelola jenis sampah yang diterima dan perbarui tarif harga harian per kg</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Kolom 1 & 2: Daftar Jenis & Update Harga -->
        <div class="md:col-span-2 space-y-6">
            <!-- Daftar Jenis Sampah & Harga Berlaku -->
            <div class="bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="font-bold text-sm text-gray-800">Jenis Sampah & Harga Berlaku</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-600 uppercase border-b border-gray-200">
                            <tr>
                                <th class="py-2.5 px-3">Jenis Sampah</th>
                                <th class="py-2.5 px-3">Satuan</th>
                                <th class="py-2.5 px-3">Harga Berlaku</th>
                                <th class="py-2.5 px-3">Status</th>
                                <th class="py-2.5 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($jenisList as $jenis)
                                <tr>
                                    <td class="py-2.5 px-3 font-bold text-gray-800">{{ $jenis->nama }}</td>
                                    <td class="py-2.5 px-3">{{ $jenis->satuan }}</td>
                                    <td class="py-2.5 px-3 font-semibold text-emerald-700">
                                        @if($jenis->harga_berlaku)
                                            Rp {{ number_format($jenis->harga_berlaku->harga_per_kg, 0, ',', '.') }} / {{ $jenis->satuan }}
                                            <div class="text-[10px] text-gray-400 font-normal">Mulai {{ $jenis->harga_berlaku->berlaku_mulai->format('d/m/Y') }}</div>
                                        @else
                                            <span class="text-red-500 font-normal">Belum ditentukan</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $jenis->aktif ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700' }}">
                                            {{ $jenis->aktif ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-right">
                                        <button type="button" onclick="document.getElementById('edit-jenis-{{ $jenis->id }}').classList.toggle('hidden')"
                                                class="text-blue-600 hover:underline">
                                            Ubah
                                        </button>
                                    </td>
                                </tr>
                                <!-- Baris Edit Inline -->
                                <tr id="edit-jenis-{{ $jenis->id }}" class="hidden bg-gray-50">
                                    <td colspan="5" class="p-3">
                                        <form method="POST" action="{{ route('admin.katalog.jenis.update', $jenis->id) }}" class="flex flex-wrap items-center gap-3">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <input type="text" name="nama" value="{{ $jenis->nama }}" required class="px-2 py-1 border rounded text-xs w-36">
                                            </div>
                                            <div>
                                                <input type="text" name="satuan" value="{{ $jenis->satuan }}" required class="px-2 py-1 border rounded text-xs w-20">
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <input type="checkbox" name="aktif" value="1" id="aktif-{{ $jenis->id }}" {{ $jenis->aktif ? 'checked' : '' }} class="rounded text-emerald-600">
                                                <label for="aktif-{{ $jenis->id }}" class="text-xs text-gray-700">Aktif</label>
                                            </div>
                                            <button type="submit" class="bg-emerald-700 text-white px-3 py-1 rounded text-xs font-semibold">Simpan</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Perbarui Harga Harian (F-13) -->
            <div class="bg-white p-5 rounded border border-gray-200 shadow-sm">
                <h2 class="font-bold text-sm text-gray-800 mb-2">Perbarui Tarif Harga Sampah (F-13)</h2>
                <p class="text-xs text-gray-500 mb-4">Setiap perubahan harga akan tersimpan sebagai riwayat baru tanpa mengubah nilai transaksi lampau (BR-04).</p>

                <form method="POST" action="{{ route('admin.katalog.harga.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Pilih Jenis Sampah</label>
                        <select name="jenis_sampah_id" required class="w-full px-2 py-1.5 border border-gray-300 rounded focus:border-emerald-600">
                            @foreach($jenisList as $jenis)
                                <option value="{{ $jenis->id }}">{{ $jenis->nama }} (per {{ $jenis->satuan }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Harga Baru (Rp)</label>
                        <input type="number" step="0.01" name="harga_per_kg" required placeholder="Contoh: 3000"
                               class="w-full px-2 py-1.5 border border-gray-300 rounded focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Berlaku Mulai Tanggal</label>
                        <input type="date" name="berlaku_mulai" value="{{ date('Y-m-d') }}" required
                               class="w-full px-2 py-1.5 border border-gray-300 rounded focus:border-emerald-600">
                    </div>

                    <div class="md:col-span-3 pt-2">
                        <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-semibold px-4 py-2 rounded text-xs transition">
                            Simpan Tarif Harga Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kolom 3: Riwayat Pembaruan Harga (F-14) -->
        <div class="bg-white p-4 rounded border border-gray-200 shadow-sm">
            <h2 class="font-bold text-sm text-gray-800 mb-3">Riwayat Pembaruan Harga (F-14)</h2>
            <div class="space-y-3 overflow-y-auto max-h-[500px] text-xs divide-y divide-gray-100">
                @forelse($riwayatHargaTerbaru as $h)
                    <div class="pt-2">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-800">{{ $h->jenisSampah->nama }}</span>
                            <span class="font-semibold text-emerald-700">Rp {{ number_format($h->harga_per_kg, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-[11px] text-gray-400 mt-0.5">
                            <span>Mulai: {{ $h->berlaku_mulai->format('d/m/Y') }}</span>
                            <span>Oleh: {{ $h->pembuat->name }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 py-4 text-center">Belum ada riwayat harga.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
