@extends('layouts.app', ['title' => 'Detail Setoran ' . $setoran->kode . ' · Bank Sampah Sahabat Ibu', 'dataView' => 'riwayat', 'pageTitle' => 'Detail Setoran Sampah'])

@section('content')
<small class="kick">Riwayat</small>
<h1>Detail setoran <em>{{ $setoran->kode }}</em></h1>
<p class="lead">
    Tanggal: <b>{{ $setoran->tanggal->format('d/m/Y H:i') }}</b> · 
    Status: <span class="pill {{ $setoran->status === 'berhasil' ? 'ok' : 'no' }}">{{ ucfirst($setoran->status) }}</span>
</p>

<div class="kpi" style="margin-bottom:24px">
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
        Setoran ini telah dibatalkan oleh pengurus pada {{ $setoran->dibatalkan_pada?->format('d/m/Y H:i') }}<br>
        Alasan: <em>{{ $setoran->alasan_pembatalan }}</em>
    </div>
@endif

<h2>Rincian sampah yang disetorkan</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Jenis Sampah</th>
                <th>Harga per satuan saat setoran</th>
                <th>Berat</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($setoran->detailSetoran as $detail)
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

<div class="row" style="margin-top:24px">
    <a href="{{ route('nasabah.setoran.index') }}" class="btn o sm" style="text-decoration:none">&larr; Kembali ke riwayat setoran</a>
</div>
@endsection
