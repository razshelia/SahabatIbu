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
        <small>Nomor nasabah</small>
        <b>{{ $setoran->nasabah->nomor_nasabah ?? '-' }}</b>
    </div>
    <div class="g">
        <small>Total berat</small>
        <b>{{ number_format($setoran->total_berat, 2, ',', '.') }} kg</b>
    </div>
    <div class="g">
        <small>Nilai setoran</small>
        <b>Rp {{ number_format($setoran->total_nilai, 0, ',', '.') }}</b>
    </div>
</div>

@if($setoran->status === 'dibatalkan')
    <div style="background:rgba(239,68,68,0.15);border:1px solid #ef4444;padding:14px;border-radius:12px;margin-bottom:24px;font-size:13px;color:#fca5a5;">
        <b>Setoran dibatalkan</b><br>
        Dibatalkan oleh: <b>{{ $setoran->dibatalkanOleh?->name ?? 'Pengurus' }}</b> pada {{ $setoran->dibatalkan_pada?->format('d/m/Y H:i') }}<br>
        Alasan: <em>{{ $setoran->alasan_pembatalan }}</em>
    </div>
@endif

<h2>Rincian sampah</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Jenis sampah</th>
                <th>Harga per satuan saat setoran</th>
                <th>Berat</th>
                <th>Subtotal</th>
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
                <td colspan="2">Total</td>
                <td>{{ number_format($setoran->total_berat, 2, ',', '.') }} kg</td>
                <td>Rp {{ number_format($setoran->total_nilai, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="row" style="margin-top:24px;gap:8px">
    <a href="{{ route('admin.setoran.index') }}" class="btn o sm" style="text-decoration:none">&larr; Kembali ke daftar setoran</a>
    
    @if($setoran->status === 'berhasil')
        <button class="btn o sm" type="button" onclick="document.getElementById('modal-batal').showModal()" style="border-color:#ef4444;color:#fca5a5">
            Batalkan setoran
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
                Pembatalan akan mengurangi saldo nasabah sebesar Rp {{ number_format($setoran->total_nilai, 0, ',', '.') }}.
            </p>

            <label for="alasan_pembatalan">
                Alasan pembatalan (wajib)
                <input id="alasan_pembatalan" name="alasan_pembatalan" type="text" maxlength="255" value="{{ old('alasan_pembatalan') }}" required placeholder="Contoh: Kesalahan timbangan">
                @error('alasan_pembatalan')<span class="err" data-validation-error-for="alasan_pembatalan">{{ $message }}</span>@enderror
            </label>

            <div class="row" style="margin-top:14px;gap:8px">
                <button class="btn" type="submit" style="background:#dc2626;border-color:#b91c1c">Batalkan setoran</button>
                <button class="btn o" type="button" onclick="document.getElementById('modal-batal').close()">Tutup</button>
            </div>
        </form>
    </dialog>
    @if($errors->has('alasan_pembatalan'))
        <script>window.onAppPageReady(() => document.getElementById('modal-batal').showModal());</script>
    @endif
@endif
@endsection
