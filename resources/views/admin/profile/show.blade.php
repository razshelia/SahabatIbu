@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Profil Admin & Log Aktivitas</h1>
            <p class="text-xs text-gray-500">Informasi pengurus dan jejak audit keamanan sistem (F-44)</p>
        </div>
        <a href="{{ route('password.change.form') }}" class="bg-gray-800 hover:bg-black text-white text-xs font-semibold px-3 py-2 rounded transition">
            Ganti Kata Sandi
        </a>
    </div>

    <!-- Info Admin -->
    <div class="bg-white p-5 rounded border border-gray-200 shadow-sm text-xs grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <span class="text-gray-400 font-semibold uppercase">Nama Pengurus</span>
            <p class="font-bold text-gray-800 text-sm mt-0.5">{{ $admin->name }}</p>
        </div>
        <div>
            <span class="text-gray-400 font-semibold uppercase">Alamat Email</span>
            <p class="font-bold text-gray-800 text-sm mt-0.5">{{ $admin->email }}</p>
        </div>
        <div>
            <span class="text-gray-400 font-semibold uppercase">Peran & Status</span>
            <p class="font-bold text-emerald-700 text-sm mt-0.5 uppercase">{{ $admin->role }} &bull; {{ $admin->status }}</p>
        </div>
    </div>

    <!-- Tabel Log Aktivitas (F-44) -->
    <div class="bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-bold text-sm text-gray-800">Jejak Log Aktivitas Sistem (Audit Trail)</h2>
            <form method="GET" action="{{ route('admin.profile.show') }}" class="flex gap-2">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari aksi, pelaku, IP..."
                       class="px-2 py-1 border border-gray-300 rounded text-xs">
                <button type="submit" class="bg-gray-800 text-white px-2.5 py-1 rounded text-xs">Cari</button>
                @if($search)
                    <a href="{{ route('admin.profile.show') }}" class="text-xs text-gray-500 hover:underline self-center">Reset</a>
                @endif
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase border-b border-gray-200">
                    <tr>
                        <th class="py-2 px-3">Waktu</th>
                        <th class="py-2 px-3">Pelaku</th>
                        <th class="py-2 px-3">Aksi</th>
                        <th class="py-2 px-3">Objek</th>
                        <th class="py-2 px-3">Keterangan</th>
                        <th class="py-2 px-3">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-mono text-[11px]">
                    @forelse($logs as $log)
                        <tr>
                            <td class="py-2 px-3 text-gray-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="py-2 px-3 font-semibold text-gray-800 font-sans">{{ $log->user?->name ?? ($log->user_id ? 'User #' . $log->user_id : 'Sistem/Tamu') }}</td>
                            <td class="py-2 px-3">
                                <span class="bg-gray-100 px-1.5 py-0.5 rounded font-bold text-gray-700">{{ $log->aksi }}</span>
                            </td>
                            <td class="py-2 px-3 text-gray-600">{{ $log->objek_tipe ? "{$log->objek_tipe} #{$log->objek_id}" : '-' }}</td>
                            <td class="py-2 px-3 text-gray-500 max-w-xs truncate font-sans">
                                {{ is_array($log->keterangan) ? json_encode($log->keterangan) : ($log->keterangan ?? '-') }}
                            </td>
                            <td class="py-2 px-3 text-gray-500 whitespace-nowrap">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-400 font-sans">Belum ada catatan log aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
