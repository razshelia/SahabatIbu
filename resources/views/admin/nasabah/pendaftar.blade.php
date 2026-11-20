@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Verifikasi Pendaftar Nasabah</h1>
            <p class="text-xs text-gray-500">Daftar calon nasabah yang menunggu persetujuan pengurus</p>
        </div>
        <a href="{{ route('admin.nasabah.index') }}" class="text-xs text-emerald-700 hover:underline">
            &larr; Kembali ke Data Nasabah
        </a>
    </div>

    <div class="bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase border-b border-gray-200">
                    <tr>
                        <th class="py-2.5 px-3">Tanggal Daftar</th>
                        <th class="py-2.5 px-3">Nama Pendaftar</th>
                        <th class="py-2.5 px-3">Email & Kontak</th>
                        <th class="py-2.5 px-3">Alamat</th>
                        <th class="py-2.5 px-3 text-right">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pendaftarList as $pendaftar)
                        <tr>
                            <td class="py-2.5 px-3 text-gray-500 whitespace-nowrap">{{ $pendaftar->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2.5 px-3 font-bold text-gray-800">{{ $pendaftar->name }}</td>
                            <td class="py-2.5 px-3">
                                <div>{{ $pendaftar->email }}</div>
                                <div class="text-gray-500 font-mono">{{ $pendaftar->nasabah?->telepon ?? '-' }}</div>
                            </td>
                            <td class="py-2.5 px-3 text-gray-600 max-w-xs">{{ $pendaftar->nasabah?->alamat ?? '-' }}</td>
                            <td class="py-2.5 px-3 text-right space-x-2 whitespace-nowrap">
                                <!-- Tombol Setujui -->
                                <form method="POST" action="{{ route('admin.nasabah.verifikasi', $pendaftar->id) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="aksi" value="setujui">
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-3 py-1 rounded text-xs"
                                            onclick="return confirm('Setujui pendaftaran {{ $pendaftar->name }}?')">
                                        Setujui
                                    </button>
                                </form>

                                <!-- Tombol Tolak Modal Trigger -->
                                <button type="button" onclick="document.getElementById('modal-tolak-{{ $pendaftar->id }}').classList.remove('hidden')"
                                        class="bg-red-600 hover:bg-red-700 text-white font-medium px-3 py-1 rounded text-xs">
                                    Tolak
                                </button>

                                <!-- Modal Input Alasan Penolakan -->
                                <div id="modal-tolak-{{ $pendaftar->id }}" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden text-left whitespace-normal">
                                    <div class="bg-white rounded-lg p-5 max-w-sm w-full space-y-3">
                                        <h3 class="font-bold text-gray-800 text-sm">Tolak Pendaftaran {{ $pendaftar->name }}</h3>
                                        <form method="POST" action="{{ route('admin.nasabah.verifikasi', $pendaftar->id) }}" class="space-y-3">
                                            @csrf
                                            <input type="hidden" name="aksi" value="tolak">
                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Alasan Penolakan (Wajib):</label>
                                                <textarea name="alasan" rows="3" required placeholder="Contoh: Bukan warga RT/RW setempat"
                                                          class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs"></textarea>
                                            </div>
                                            <div class="flex justify-end gap-2 pt-2">
                                                <button type="button" onclick="document.getElementById('modal-tolak-{{ $pendaftar->id }}').classList.add('hidden')"
                                                        class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-xs">Batal</button>
                                                <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-semibold">
                                                    Kirim Penolakan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-400">Tidak ada pendaftar baru yang menunggu verifikasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-200">
            {{ $pendaftarList->links() }}
        </div>
    </div>
</div>
@endsection
