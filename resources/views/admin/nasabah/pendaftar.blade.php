@extends('layouts.app', ['title' => 'Pendaftar & Nasabah · Bank Sampah Sahabat Ibu', 'dataView' => 'scan', 'pageTitle' => 'Pendaftar & Nasabah'])

@section('content')
<small class="kick">Pendaftar &amp; nasabah</small>
<h1>Semua <em>pendaftaran</em></h1>
<p class="lead">Daftar ini memuat semua nasabah, baik yang menunggu verifikasi maupun yang sudah diproses.</p>

<x-alat tool="scan" plat="Semua pendaftaran">
    <x-slot:layar>
        <small>Total nasabah terdaftar</small>
        <b class="angka">{{ $pendaftarList->total() }} orang</b>
    </x-slot:layar>
    <div class="deret">
        <a href="{{ route('admin.nasabah.create') }}" class="key g">+ Tambah nasabah</a>
        <a href="{{ route('admin.nasabah.index') }}" class="key p">&larr; Semua nasabah</a>
    </div>
</x-alat>

<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Kategori</th>
                <th>Alamat</th>
                <th>Tanggal daftar</th>
                <th>Status verifikasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendaftarList as $pendaftar)
                <tr>
                    <td><b>{{ $pendaftar->name }}</b></td>
                    <td>{{ $pendaftar->email }}</td>
                    <td>{{ $pendaftar->nasabah->telepon ?? '-' }}</td>
                    <td><span class="pill {{ ($pendaftar->nasabah->kategori ?? 'warga') === 'umum' ? 'no' : 'ok' }}">{{ ($pendaftar->nasabah->kategori ?? 'warga') === 'umum' ? 'Umum' : 'Warga' }}</span></td>
                    <td>{{ $pendaftar->nasabah->alamat ?? '-' }}</td>
                    <td>{{ $pendaftar->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($pendaftar->status === 'menunggu')
                            <span class="pill kuning">Belum diverifikasi</span>
                        @elseif($pendaftar->status === 'aktif')
                            <span class="pill ok">Disetujui</span>
                        @elseif($pendaftar->status === 'nonaktif')
                            <span class="pill no">Disetujui · Nonaktif</span>
                        @else
                            <span class="pill no">{{ ucfirst($pendaftar->status) }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="row" style="gap:6px">
                            @if($pendaftar->status === 'menunggu')
                                <form method="POST" action="{{ route('admin.nasabah.verifikasi', $pendaftar) }}" style="margin:0">
                                    @csrf
                                    <input type="hidden" name="aksi" value="setujui">
                                    <button class="btn sm" type="submit">Setujui</button>
                                </form>
                                <button class="btn o sm" type="button" data-id="{{ $pendaftar->id }}" data-nama="{{ $pendaftar->name }}" onclick="bukaModalTolakDariTombol(this)">
                                    Tolak
                                </button>
                            @elseif(in_array($pendaftar->status, ['aktif', 'nonaktif']))
                                <a href="{{ route('admin.nasabah.index', ['q' => $pendaftar->email]) }}" class="btn o sm" style="text-decoration:none">Lihat nasabah</a>
                            @else
                                <small>Sudah diproses</small>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">Belum ada data pendaftaran nasabah.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">
    {{ $pendaftarList->links() }}
</div>

<dialog id="modal-tolak" class="dlg-alat">
    <x-alat tool="scan" plat="Tolak Pendaftar">
        <x-slot:layar>
            <small>Pendaftar</small>
            <b class="angka kecil kuning" id="nama-pendaftar-tolak">-</b>
        </x-slot:layar>
        <form method="POST" id="form-tolak" style="display:grid;gap:16px">
            @csrf
            <input type="hidden" name="pendaftar_id" id="pendaftar-tolak-id" value="{{ old('pendaftar_id') }}">
            <input type="hidden" name="pendaftar_tolak_label" id="pendaftar-tolak-label" value="{{ old('pendaftar_tolak_label') }}">
            <input type="hidden" name="aksi" value="tolak">
            <label for="alasan">
                Catatan untuk pendaftar (wajib)
                <input id="alasan" name="alasan" type="text" maxlength="255" value="{{ old('alasan') }}" required placeholder="Tuliskan alasan pendaftaran belum disetujui">
                @error('alasan')<span class="err" data-validation-error-for="alasan">{{ $message }}</span>@enderror
            </label>
            <div class="deret">
                <button class="key r besar" type="submit">Tolak pendaftaran</button>
                <button class="key p" type="button" onclick="document.getElementById('modal-tolak').close()">Batal</button>
            </div>
        </form>
    </x-alat>
</dialog>

<script>
    function bukaModalTolak(id, nama) {
        const modal = document.getElementById('modal-tolak');
        const form = document.getElementById('form-tolak');
        document.getElementById('nama-pendaftar-tolak').textContent = nama;
        document.getElementById('pendaftar-tolak-id').value = id;
        document.getElementById('pendaftar-tolak-label').value = nama;
        form.action = "{{ url('admin/nasabah') }}/" + id + "/verifikasi";
        modal.showModal();
    }

    function bukaModalTolakDariTombol(button) {
        bukaModalTolak(Number(button.dataset.id), button.dataset.nama);
    }

</script>
@if($errors->has('alasan') && old('pendaftar_id'))
    <script>
        window.onAppPageReady(() => {
            const id = Number(document.getElementById('pendaftar-tolak-id').value);
            if (!Number.isInteger(id) || id < 1) return;
            document.getElementById('nama-pendaftar-tolak').textContent = document.getElementById('pendaftar-tolak-label').value;
            document.getElementById('form-tolak').action = "{{ url('admin/nasabah') }}/" + id + "/verifikasi";
            document.getElementById('modal-tolak').showModal();
        });
    </script>
@endif
@endsection
