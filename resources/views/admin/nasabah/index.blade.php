@extends('layouts.app', ['title' => 'Daftar Nasabah · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Kelola Nasabah'])

@section('content')
<small class="kick">Pendaftar &amp; nasabah</small>
<h1>Daftar <em>nasabah</em></h1>

<div class="row" style="margin-bottom:16px;gap:8px">
    <a href="{{ route('admin.nasabah.create') }}" class="btn sm" style="text-decoration:none">+ Tambah nasabah</a>
    <a href="{{ route('admin.nasabah.pendaftar') }}" class="btn o sm" style="text-decoration:none">
        Periksa pendaftar
        @php
            $jmlMenunggu = \App\Models\User::where('role', 'nasabah')->where('status', 'menunggu')->count();
        @endphp
        @if($jmlMenunggu > 0)
            <span class="pill" style="margin-left:6px;padding:2px 8px">{{ $jmlMenunggu }}</span>
        @endif
    </a>
</div>

<form class="f frow" method="GET" action="{{ route('admin.nasabah.index') }}" style="margin-bottom:20px">
    <label>
        Cari
        <input name="q" type="search" placeholder="Cari nama, nomor, atau kontak..." value="{{ request('q') }}">
    </label>
    <label>
        Status
        <select name="status">
            <option value="">Semua status</option>
            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
    </label>
    <button class="btn sm" type="submit">Filter</button>
    @if(request('q') || request('status'))
        <a href="{{ route('admin.nasabah.index') }}" class="btn o sm" style="text-decoration:none">Reset</a>
    @endif
</form>

<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Nomor nasabah</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Kategori</th>
                <th>Saldo</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nasabahList as $nasabah)
                <tr>
                    <td><b>{{ $nasabah->nomor_nasabah }}</b></td>
                    <td>{{ $nasabah->user->name }}</td>
                    <td>{{ $nasabah->user->email }}</td>
                    <td>{{ $nasabah->telepon }}</td>
                    <td>{{ $nasabah->kategori === 'umum' ? 'Umum' : 'Warga' }}</td>
                    <td>Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</td>
                    <td>
                        <span class="pill {{ $nasabah->user->status === 'aktif' ? 'ok' : 'no' }}">
                            {{ ucfirst($nasabah->user->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="row" style="gap:6px">
                            @if(!str_starts_with($nasabah->nomor_nasabah, 'ANON-'))
                                <a href="{{ route('admin.nasabah.edit', $nasabah) }}" class="btn o sm" style="text-decoration:none">Ubah</a>

                                @if(in_array($nasabah->user->status, ['aktif', 'nonaktif']))
                    <form method="POST" action="{{ route('admin.nasabah.toggle-status', $nasabah->user) }}" style="margin:0">
                        @csrf
                        <button class="btn o sm" type="submit">
                            {{ $nasabah->user->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form> @elseif($nasabah->user->status === 'menunggu')
                    <a href="{{ route('admin.nasabah.pendaftar') }}" class="btn sm" style="text-decoration:none">Verifikasi</a> @endif

                                @if($nasabah->user->status === 'nonaktif' && $nasabah->saldo == 0)
                                    <button class="btn o sm" type="button" data-id="{{ $nasabah->id }}" data-nomor="{{ $nasabah->nomor_nasabah }}" data-nama="{{ $nasabah->user->name }}" onclick="bukaModalAnonimDariTombol(this)">
                                        Anonimkan
                                    </button>
                                @endif
                            @else
                                <small style="color:var(--mu)">Sudah dianonimkan</small>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">Tidak ada data nasabah yang sesuai.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">
    {{ $nasabahList->links() }}
</div>

<dialog id="modal-anonim">
    <form method="POST" id="form-anonim" class="f" style="min-width:320px">
        @csrf
        <input type="hidden" name="nasabah_id" id="nasabah-anonim-id" value="{{ old('nasabah_id') }}">
        <input type="hidden" name="nasabah_anonim_label" id="nasabah-anonim-label" value="{{ old('nasabah_anonim_label') }}">
        <h3>Anonimkan data nasabah</h3>
        <p class="lead" id="ket-anonim" style="margin-bottom:12px;color:#fca5a5"></p>
        <p style="font-size:12px;color:var(--mu);margin-bottom:12px">
            Tindakan ini tidak dapat dibatalkan. Nama, email, telepon, alamat, dan alamat IP akan diganti dengan data anonim sesuai ketentuan perlindungan data.
            Ketik <b>ANONIMKAN</b> untuk melanjutkan:
        </p>

        <label for="konfirmasi">
            Ketik konfirmasi
            <input id="konfirmasi" name="konfirmasi" type="text" value="{{ old('konfirmasi') }}" required data-exact-value="ANONIMKAN" placeholder="ANONIMKAN">
            @error('konfirmasi')<span class="err" data-validation-error-for="konfirmasi">{{ $message }}</span>@enderror
        </label>

        <div class="row" style="margin-top:14px;gap:8px">
            <button class="btn" type="submit" style="background:#dc2626;border-color:#b91c1c">Anonimkan data</button>
            <button class="btn o" type="button" onclick="document.getElementById('modal-anonim').close()">Batal</button>
        </div>
    </form>
</dialog>

<script>
    function bukaModalAnonim(id, nomor, nama) {
        const modal = document.getElementById('modal-anonim');
        const form = document.getElementById('form-anonim');
        document.getElementById('ket-anonim').textContent = nomor + ' · ' + nama;
        document.getElementById('nasabah-anonim-id').value = id;
        document.getElementById('nasabah-anonim-label').value = nomor + ' · ' + nama;
        form.action = "{{ url('admin/nasabah') }}/" + id + "/anonimkan";
        modal.showModal();
    }

    function bukaModalAnonimDariTombol(button) {
        bukaModalAnonim(Number(button.dataset.id), button.dataset.nomor, button.dataset.nama);
    }
</script>
@if($errors->has('konfirmasi') && old('nasabah_id'))
    <script>
        window.onAppPageReady(() => {
            const id = Number(document.getElementById('nasabah-anonim-id').value);
            if (!Number.isInteger(id) || id < 1) return;
            document.getElementById('ket-anonim').textContent = document.getElementById('nasabah-anonim-label').value;
            document.getElementById('form-anonim').action = "{{ url('admin/nasabah') }}/" + id + "/anonimkan";
            document.getElementById('modal-anonim').showModal();
        });
    </script>
@endif
@endsection
