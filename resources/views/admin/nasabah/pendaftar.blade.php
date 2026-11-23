@extends('layouts.app', ['title' => 'Verifikasi Pendaftar · Bank Sampah Sahabat Ibu', 'dataView' => 'scan', 'pageTitle' => 'Verifikasi Pendaftar'])

@section('content')
<small class="kick">Pendaftar &amp; nasabah</small>
<h1>Verifikasi <em>pendaftar</em></h1>
<p class="lead">Daftar calon nasabah baru yang menunggu verifikasi pengurus.</p>

<div class="row" style="margin-bottom:20px">
    <a href="{{ route('admin.nasabah.index') }}" class="btn o sm" style="text-decoration:none">&larr; Daftar Nasabah</a>
    <a href="{{ route('admin.nasabah.create') }}" class="btn sm" style="text-decoration:none">+ Tambah Nasabah Langsung</a>
</div>

<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Kategori</th>
                <th>Alamat</th>
                <th>Waktu Daftar</th>
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
                        <div class="row" style="gap:6px">
                            <form method="POST" action="{{ route('admin.nasabah.verifikasi', $pendaftar) }}" style="margin:0">
                                @csrf
                                <input type="hidden" name="aksi" value="setujui">
                                <button class="btn sm" type="submit">Setujui</button>
                            </form>

                            <button class="btn o sm" type="button" onclick="bukaModalTolak({{ $pendaftar->id }}, '{{ addslashes($pendaftar->name) }}')">
                                Tolak
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">Tidak ada pendaftar yang menunggu verifikasi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">
    {{ $pendaftarList->links() }}
</div>

<dialog id="modal-tolak">
    <form method="POST" id="form-tolak" class="f" style="min-width:320px">
        @csrf
        <h3>Tolak Pendaftar</h3>
        <p class="lead" id="nama-pendaftar-tolak" style="margin-bottom:12px"></p>
        <input type="hidden" name="aksi" value="tolak">
        
        <label for="alasan">
            Alasan penolakan (wajib)
            <input id="alasan" name="alasan" type="text" maxlength="255" required placeholder="Contoh: Bukan warga wilayah jangkauan RT/RW">
        </label>

        <div class="row" style="margin-top:14px;gap:8px">
            <button class="btn" type="submit" style="background:#dc2626;border-color:#b91c1c">Kirim Penolakan</button>
            <button class="btn o" type="button" onclick="document.getElementById('modal-tolak').close()">Batal</button>
        </div>
    </form>
</dialog>

<script>
    function bukaModalTolak(id, nama) {
        const modal = document.getElementById('modal-tolak');
        const form = document.getElementById('form-tolak');
        document.getElementById('nama-pendaftar-tolak').textContent = 'Calon Nasabah: ' + nama;
        form.action = "{{ url('admin/nasabah') }}/" + id + "/verifikasi";
        modal.showModal();
    }
</script>
@endsection
