@extends('layouts.app', ['title' => 'Detail Setoran ' . $setoran->kode . ' · Bank Sampah Sahabat Ibu', 'dataView' => 'timbang', 'pageTitle' => 'Detail Transaksi Setoran'])

@section('content')
<small class="kick">Setoran</small>
<h1>Detail setoran <em>{{ $setoran->kode }}</em></h1>
<p class="lead">
    Status: <span class="pill {{ $setoran->status === 'berhasil' ? 'ok' : 'no' }}">{{ ucfirst($setoran->status) }}</span>
    · Tanggal: <b>{{ $setoran->tanggal->format('d/m/Y H:i') }}</b>
</p>

<div class="kpi" style="margin-bottom:24px">
    <div class="g">
        <small>Nasabah</small>
        <b>{{ $setoran->nasabah->user->name ?? '-' }}</b>
    </div>
    <div class="g">
        <small>Nomor Nasabah</small>
        <b>{{ $setoran->nasabah->nomor_nasabah ?? '-' }}</b>
    </div>
    <div class="g">
        <small>Total Berat</small>
        <b>{{ number_format($setoran->total_berat, 2, ',', '.') }} kg</b>
    </div>
    <div class="g">
        <small>Total Nilai</small>
        <b>Rp {{ number_format($setoran->total_nilai, 0, ',', '.') }}</b>
    </div>
</div>

@if($setoran->status === 'dibatalkan')
    <div style="background:rgba(239,68,68,0.15);border:1px solid #ef4444;padding:14px;border-radius:12px;margin-bottom:24px;font-size:13px;color:#fca5a5;">
        <b>Informasi Pembatalan:</b><br>
        Dibatalkan oleh: <b>{{ $setoran->dibatalkanOleh?->name ?? 'Admin' }}</b> pada {{ $setoran->dibatalkan_pada?->format('d/m/Y H:i') }}<br>
        Alasan: <em>{{ $setoran->alasan_pembatalan }}</em>
    </div>
@endif

<h2>Rincian item sampah (Snapshot Harga BR-04)</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Jenis Sampah</th>
                <th>Harga Terkunci (Snapshot)</th>
                <th>Berat Ditimbang</th>
                <th>Subtotal Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($setoran->detail as $detail)
                <tr>
                    <td><b>{{ $detail->jenisSampah->nama ?? '-' }}</b></td>
                    <td>Rp {{ number_format($detail->harga_per_kg, 0, ',', '.') }} / {{ $detail->jenisSampah->satuan ?? 'kg' }}</td>
                    <td>{{ number_format($detail->berat_kg, 2, ',', '.') }} {{ $detail->jenisSampah->satuan ?? 'kg' }}</td>
                    <td><b>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</b></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="border-top:1px solid var(--bd);font-weight:bold">
                <td colspan="2">TOTAL</td>
                <td>{{ number_format($setoran->total_berat, 2, ',', '.') }} kg</td>
                <td>Rp {{ number_format($setoran->total_nilai, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="row" style="margin-top:24px;gap:8px">
    <a href="{{ route('admin.setoran.index') }}" class="btn o sm" style="text-decoration:none">&larr; Kembali ke Daftar Setoran</a>
    
    @if($setoran->status === 'berhasil')
        <button class="btn o sm" type="button" onclick="document.getElementById('modal-batal').showModal()" style="border-color:#ef4444;color:#fca5a5">
            Batalkan Transaksi
        </button>
    @endif
</div>

@if($setoran->status === 'berhasil')
    <dialog id="modal-batal">
        <form method="POST" action="{{ route('admin.setoran.batalkan', $setoran) }}" class="f" style="min-width:320px">
            @csrf
            <h3>Batalkan Setoran</h3>
            <p class="lead" style="margin-bottom:12px">{{ $setoran->kode }} · {{ $setoran->nasabah->user->name ?? '' }}</p>
            <p style="font-size:12px;color:var(--mu);margin-bottom:12px">
                Pembatalan akan mengurangi saldo nasabah sebesar Rp {{ number_format($setoran->total_nilai, 0, ',', '.') }} (BR-10, BR-17).
            </p>

            <label for="alasan_pembatalan">
                Alasan pembatalan (wajib)
                <input id="alasan_pembatalan" name="alasan_pembatalan" type="text" maxlength="255" required placeholder="Contoh: Kesalahan timbangan">
            </label>

            <div class="row" style="margin-top:14px;gap:8px">
                <button class="btn" type="submit" style="background:#dc2626;border-color:#b91c1c">Konfirmasi Batal</button>
                <button class="btn o" type="button" onclick="document.getElementById('modal-batal').close()">Tutup</button>
            </div>
        </form>
    </dialog>
@endif
@endsection
