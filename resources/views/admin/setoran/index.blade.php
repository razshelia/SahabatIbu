@extends('layouts.app', ['title' => 'Setoran · Bank Sampah Sahabat Ibu', 'dataView' => 'timbang', 'pageTitle' => 'Setoran Sampah'])

@section('content')
<small class="kick">Setoran</small>
<h1>Riwayat &amp; data <em>setoran</em></h1>

<div class="row" style="margin-bottom:16px">
    <a href="{{ route('admin.setoran.create') }}" class="btn sm" style="text-decoration:none">+ Catat Setoran Baru</a>
</div>

<form class="f frow" method="GET" action="{{ route('admin.setoran.index') }}" style="margin-bottom:20px">
    <label>
        Nasabah
        <select name="nasabah_id">
            <option value="">Semua Nasabah</option>
            @foreach($nasabahList as $n)
                <option value="{{ $n->id }}" {{ request('nasabah_id') == $n->id ? 'selected' : '' }}>
                    {{ $n->nomor_nasabah }} · {{ $n->user->name }}
                </option>
            @endforeach
        </select>
    </label>

    <label>
        Dari Tanggal
        <input name="start_date" type="date" value="{{ request('start_date') }}">
    </label>

    <label>
        Sampai Tanggal
        <input name="end_date" type="date" value="{{ request('end_date') }}">
    </label>

    <label>
        Status
        <select name="status">
            <option value="">Semua Status</option>
            <option value="berhasil" {{ request('status') === 'berhasil' ? 'selected' : '' }}>Berhasil</option>
            <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
    </label>

    <button class="btn sm" type="submit">Filter</button>
    @if(request('nasabah_id') || request('start_date') || request('end_date') || request('status'))
        <a href="{{ route('admin.setoran.index') }}" class="btn o sm" style="text-decoration:none">Reset</a>
    @endif
</form>

<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Nasabah</th>
                <th>Total Berat</th>
                <th>Nilai (Rp)</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksiList as $setoran)
                <tr>
                    <td><b>{{ $setoran->kode }}</b></td>
                    <td>{{ $setoran->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $setoran->nasabah->user->name ?? '-' }}</td>
                    <td>{{ number_format($setoran->total_berat, 2, ',', '.') }} kg</td>
                    <td>Rp {{ number_format($setoran->total_nilai, 0, ',', '.') }}</td>
                    <td>
                        <span class="pill {{ $setoran->status === 'berhasil' ? 'ok' : 'no' }}">
                            {{ ucfirst($setoran->status) }}
                        </span>
                        @if($setoran->status === 'dibatalkan' && $setoran->alasan_pembatalan)
                            <small style="color:var(--mu);display:block">Alasan: {{ $setoran->alasan_pembatalan }}</small>
                        @endif
                    </td>
                    <td>
                        <div class="row" style="gap:6px">
                            <a href="{{ route('admin.setoran.show', $setoran) }}" class="btn o sm" style="text-decoration:none">Detail</a>

                            @if($setoran->status === 'berhasil')
                                <button class="btn o sm" type="button" onclick="bukaModalBatal({{ $setoran->id }}, '{{ addslashes($setoran->kode) }}', '{{ addslashes($setoran->nasabah->user->name ?? '') }}')">
                                    Batalkan
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">Belum ada transaksi setoran yang cocok.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">
    {{ $transaksiList->links() }}
</div>

<dialog id="modal-batal">
    <form method="POST" id="form-batal" class="f" style="min-width:320px">
        @csrf
        <h3>Batalkan Setoran</h3>
        <p class="lead" id="ket-setoran-batal" style="margin-bottom:12px"></p>
        <p style="font-size:12px;color:var(--mu);margin-bottom:12px">
            Pembatalan akan mengurangi saldo nasabah sebesar nilai transaksi ini (BR-10, BR-17).
        </p>

        <label for="alasan_pembatalan">
            Alasan pembatalan (wajib)
            <input id="alasan_pembatalan" name="alasan_pembatalan" type="text" maxlength="255" required placeholder="Contoh: Kesalahan timbang atau permintaan nasabah">
        </label>

        <div class="row" style="margin-top:14px;gap:8px">
            <button class="btn" type="submit" style="background:#dc2626;border-color:#b91c1c">Konfirmasi Batal</button>
            <button class="btn o" type="button" onclick="document.getElementById('modal-batal').close()">Tutup</button>
        </div>
    </form>
</dialog>

<script>
    function bukaModalBatal(id, kode, nama) {
        const modal = document.getElementById('modal-batal');
        const form = document.getElementById('form-batal');
        document.getElementById('ket-setoran-batal').textContent = kode + ' · ' + nama;
        form.action = "{{ url('admin/setoran') }}/" + id + "/batalkan";
        modal.showModal();
    }
</script>
@endsection
