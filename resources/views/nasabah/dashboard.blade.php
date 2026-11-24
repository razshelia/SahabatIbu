@extends('layouts.app', ['title' => 'Ringkasan Nasabah · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Ringkasan nasabah'])

@section('content')
<small class="kick">Dashboard</small>
<h1>Halo, <em>{{ $user->name }}</em></h1>
<p class="lead">Nomor nasabah: <b>{{ $nasabah->nomor_nasabah }}</b></p>

<x-alat tool="scan" plat="Cek saldo">
    <x-slot:layar>
        <small>Saldo tersedia</small>
        <b class="angka">Rp {{ number_format($nasabah->saldo_tersedia, 0, ',', '.') }}</b>
        <div class="kisi" style="margin-top:14px">
            <div>
                <small>Sedang ditahan</small>
                <b class="angka kecil kuning">Rp {{ number_format($nasabah->saldo_ditahan, 0, ',', '.') }}</b>
            </div>
            <div>
                <small>Total berat setoran</small>
                <b class="angka kecil">{{ number_format($totalBeratSetoran, 2, ',', '.') }} kg</b>
            </div>
            <div>
                <small>Total setoran</small>
                <b class="angka kecil">Rp {{ number_format($totalNilaiSetoran, 0, ',', '.') }}</b>
            </div>
        </div>
    </x-slot:layar>

    <div class="deret">
        <a href="{{ route('nasabah.penarikan.index') }}" class="key r besar">Ajukan penarikan</a>
        <a href="{{ route('nasabah.setoran.index') }}" class="key p">Riwayat setoran</a>
        <a href="{{ route('nasabah.katalog.index') }}" class="key p">Harga hari ini</a>
    </div>
</x-alat>

<h2>Setoran terbaru</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Total berat</th>
                <th>Nilai (Rp)</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($setoranTerbaru as $setoran)
                <tr>
                    <td><b>{{ $setoran->kode }}</b></td>
                    <td>{{ $setoran->tanggal->format('d/m/Y') }}</td>
                    <td>{{ number_format($setoran->total_berat, 2, ',', '.') }} kg</td>
                    <td>Rp {{ number_format($setoran->total_nilai, 0, ',', '.') }}</td>
                    <td>
                        <span class="pill {{ $setoran->status === 'berhasil' ? 'ok' : 'no' }}">
                            {{ ucfirst($setoran->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('nasabah.setoran.show', $setoran) }}" class="btn o sm" style="text-decoration:none">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Belum ada setoran yang tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<h2>Pengajuan penarikan aktif</h2>
@if($penarikanAktif->isNotEmpty())
    <div class="tbl">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Diajukan</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penarikanAktif as $tarik)
                    <tr>
                        <td><b>{{ $tarik->kode }}</b></td>
                        <td>{{ $tarik->diajukan_pada->format('d/m/Y H:i') }}</td>
                        <td><b>Rp {{ number_format($tarik->nominal, 0, ',', '.') }}</b></td>
                        <td>
                            <span class="pill {{ $tarik->status === 'disetujui' ? 'ok' : '' }}">
                                {{ ucfirst($tarik->status) }}
                            </span>
                        </td>
                        <td>
                            @if($tarik->status === 'menunggu')
                                <form method="POST" action="{{ route('nasabah.penarikan.batalkan', $tarik) }}" style="margin:0">
                                    @csrf
                                    <button class="btn o sm" type="submit" onclick="return confirm('Batalkan pengajuan penarikan ini?')">
                                        Batalkan
                                    </button>
                                </form>
                            @elseif($tarik->metode === 'transfer')
                                <small style="color:var(--em)">Sedang diproses untuk transfer</small>
                            @else
                                <small style="color:var(--em)">Silakan temui pengurus untuk menerima uang</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="lead">Belum ada pengajuan penarikan yang aktif.</p>
@endif
@endsection
