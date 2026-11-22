@extends('layouts.app', ['title' => 'Laporan Rekapitulasi · Bank Sampah Sahabat Ibu', 'dataView' => 'riwayat', 'pageTitle' => 'Laporan Operasional'])

@section('content')
<small class="kick">Laporan</small>
<h1>Rekap <em>periode operasional</em></h1>

<form class="f frow" method="GET" action="{{ route('admin.laporan.index') }}" style="margin-bottom:20px">
    <label>
        Dari Tanggal
        <input name="start_date" type="date" value="{{ $startDate }}">
    </label>
    <label>
        Sampai Tanggal
        <input name="end_date" type="date" value="{{ $endDate }}">
    </label>
    <button class="btn sm" type="submit">Terapkan</button>
</form>

<div class="kpi">
    <div class="g">
        <small>Total setoran</small>
        <b>Rp {{ number_format($rekap['total_setoran'] ?? 0, 0, ',', '.') }}</b>
    </div>
    <div class="g">
        <small>Transaksi setoran</small>
        <b>{{ $rekap['jumlah_setoran'] ?? 0 }}</b>
    </div>
    <div class="g">
        <small>Total berat sampah</small>
        <b>{{ number_format($rekap['total_berat'] ?? 0, 2, ',', '.') }} kg</b>
    </div>
    <div class="g">
        <small>Dana dicairkan</small>
        <b>Rp {{ number_format($rekap['total_penarikan'] ?? 0, 0, ',', '.') }}</b>
    </div>
</div>

<div class="row noprint" style="margin:20px 0;gap:8px">
    <a href="{{ route('admin.laporan.export.setoran', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn sm" style="text-decoration:none">
        Ekspor CSV Setoran
    </a>
    <a href="{{ route('admin.laporan.export.penarikan', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn sm" style="text-decoration:none">
        Ekspor CSV Penarikan
    </a>
    <a href="{{ route('admin.laporan.cetak', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="btn o sm" style="text-decoration:none">
        Halaman Cetak (Print/PDF)
    </a>
</div>

<h2>Rekap per jenis sampah</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Jenis Sampah</th>
                <th>Total Berat</th>
                <th>Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapJenis as $item)
                <tr>
                    <td><b>{{ $item->nama }}</b></td>
                    <td>{{ number_format($item->total_berat, 2, ',', '.') }} {{ $item->satuan ?? 'kg' }}</td>
                    <td>Rp {{ number_format($item->total_nilai, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Tidak ada data setoran pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<h2>Transaksi setoran pada periode ini</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Nasabah</th>
                <th>Total Berat</th>
                <th>Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($setoranList as $s)
                <tr>
                    <td><b>{{ $s->kode }}</b></td>
                    <td>{{ $s->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $s->nasabah->user->name ?? '-' }}</td>
                    <td>{{ number_format($s->total_berat, 2, ',', '.') }} kg</td>
                    <td>Rp {{ number_format($s->total_nilai, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada transaksi setoran pada rentang tanggal ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin:12px 0 24px">
    {{ $setoranList->links() }}
</div>

<h2>Penarikan dana dicairkan pada periode ini</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Waktu Pencairan</th>
                <th>Nasabah</th>
                <th>Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penarikanList as $p)
                <tr>
                    <td><b>{{ $p->kode }}</b></td>
                    <td>{{ $p->dicairkan_pada?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>{{ $p->nasabah->user->name ?? '-' }}</td>
                    <td><b>Rp {{ number_format($p->nominal, 0, ',', '.') }}</b></td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada penarikan dicairkan pada rentang tanggal ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:12px">
    {{ $penarikanList->links() }}
</div>
@endsection
