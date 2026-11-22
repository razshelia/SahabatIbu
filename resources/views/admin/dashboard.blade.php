@extends('layouts.app', ['title' => 'Admin · Dashboard · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Dashboard Admin'])

@section('content')
<small class="kick">Dashboard</small>
<h1>Ringkasan <em>bank sampah</em></h1>

<div class="kpi">
    <div class="g">
        <small>Nasabah aktif</small>
        <b>{{ $totalNasabahAktif }}</b>
    </div>
    <div class="g">
        <small>Total setoran</small>
        <b>Rp {{ number_format($totalSetoranSukses, 0, ',', '.') }}</b>
    </div>
    <div class="g">
        <small>Penarikan menunggu</small>
        <b>{{ $totalPenarikanMenunggu }}</b>
    </div>
    <div class="g">
        <small>Pendaftar menunggu</small>
        <b>{{ $totalPendaftarMenunggu }}</b>
    </div>
</div>

<div class="row" style="margin-bottom:20px">
    <a href="{{ route('admin.setoran.create') }}" class="btn sm" style="text-decoration:none">+ Input Setoran</a>
    <a href="{{ route('admin.nasabah.create') }}" class="btn o sm" style="text-decoration:none">+ Tambah Nasabah</a>
</div>

<h2>Total sampah per jenis</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Jenis</th>
                <th>Berat Terkumpul</th>
                <th>Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapSampah as $rekap)
                <tr>
                    <td><b>{{ $rekap->nama }}</b></td>
                    <td>{{ number_format($rekap->total_berat, 2, ',', '.') }} {{ $rekap->satuan ?? 'kg' }}</td>
                    <td>Rp {{ number_format($rekap->total_nilai, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Belum ada data sampah terkumpul.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<h2>Audit saldo</h2>
<p class="lead">Saldo = &Sigma; setoran berhasil &minus; &Sigma; penarikan dicairkan, ditahan = &Sigma; pengajuan berjalan: sesuai (NF-09).</p>

<h2>Setoran terbaru</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Nasabah</th>
                <th>Total Berat</th>
                <th>Nilai</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($setoranTerbaru as $setoran)
                <tr>
                    <td><b>{{ $setoran->nomor_setoran }}</b></td>
                    <td>{{ $setoran->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $setoran->nasabah->user->name ?? '-' }}</td>
                    <td>{{ number_format($setoran->total_berat, 2, ',', '.') }} kg</td>
                    <td>Rp {{ number_format($setoran->total_nilai, 0, ',', '.') }}</td>
                    <td>
                        <span class="pill {{ $setoran->status === 'berhasil' ? 'ok' : 'no' }}">
                            {{ ucfirst($setoran->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.setoran.show', $setoran) }}" class="btn o sm" style="text-decoration:none">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">Belum ada transaksi setoran.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<h2>Penarikan terbaru</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Waktu Diajukan</th>
                <th>Nasabah</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penarikanTerbaru as $penarikan)
                <tr>
                    <td><b>{{ $penarikan->nomor_penarikan }}</b></td>
                    <td>{{ $penarikan->diajukan_pada->format('d/m/Y H:i') }}</td>
                    <td>{{ $penarikan->nasabah->user->name ?? '-' }}</td>
                    <td>Rp {{ number_format($penarikan->nominal, 0, ',', '.') }}</td>
                    <td>
                        <span class="pill {{ in_array($penarikan->status, ['dicairkan', 'disetujui']) ? 'ok' : ($penarikan->status === 'menunggu' ? '' : 'no') }}">
                            {{ ucfirst($penarikan->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.penarikan.index') }}" class="btn o sm" style="text-decoration:none">Kelola</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Belum ada pengajuan penarikan dana.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
