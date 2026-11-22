@extends('layouts.app', ['title' => 'Riwayat Setoran · Bank Sampah Sahabat Ibu', 'dataView' => 'riwayat', 'pageTitle' => 'Riwayat Setoran Sampah'])

@section('content')
<small class="kick">Riwayat</small>
<h1>Riwayat <em>setoran sampah</em></h1>
<p class="lead">Catatan riwayat sampah yang telah Ibu setorkan ke Bank Sampah Sahabat Ibu.</p>

<form class="f frow" method="GET" action="{{ route('nasabah.setoran.index') }}" style="margin-bottom:20px">
    <label>
        Dari Tanggal
        <input name="start_date" type="date" value="{{ request('start_date') }}">
    </label>
    <label>
        Sampai Tanggal
        <input name="end_date" type="date" value="{{ request('end_date') }}">
    </label>
    <button class="btn sm" type="submit">Terapkan</button>
    @if(request('start_date') || request('end_date'))
        <a href="{{ route('nasabah.setoran.index') }}" class="btn o sm" style="text-decoration:none">Reset</a>
    @endif
</form>

<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Tanggal</th>
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
                        <a href="{{ route('nasabah.setoran.show', $setoran) }}" class="btn o sm" style="text-decoration:none">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Belum ada riwayat setoran pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">
    {{ $transaksiList->links() }}
</div>
@endsection
