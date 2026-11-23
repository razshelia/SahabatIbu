@extends('layouts.app', ['title' => 'Penarikan Dana · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Penarikan Dana'])

@section('content')
<small class="kick">Penarikan dana</small>
<h1>Ajukan <em>ambil uang</em></h1>
<p class="lead">
    Tersedia: <b>Rp {{ number_format($nasabah->saldo_tersedia, 0, ',', '.') }}</b> · 
    Sedang ditahan: <b>Rp {{ number_format($nasabah->saldo_ditahan, 0, ',', '.') }}</b>
</p>

<h2>Formulir pengajuan</h2>
<form class="f frow" method="POST" action="{{ route('nasabah.penarikan.store') }}" style="margin-bottom:24px">
    @csrf
    @include('partials.rupiah-input', ['id' => 'nominal', 'name' => 'nominal', 'label' => 'Nominal Penarikan (Rp)', 'step' => 5000, 'max' => (int) $nasabah->saldo_tersedia, 'maxLabel' => 'Semua saldo'])
    <button class="btn sm" type="submit">Ajukan Penarikan</button>
</form>
@error('nominal')<p class="err" style="display:block;margin-top:-16px;margin-bottom:20px">{{ $message }}</p>@enderror

<h2>Riwayat pengajuan penarikan</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Waktu Pengajuan</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penarikanList as $tarik)
                <tr>
                    <td><b>{{ $tarik->kode }}</b></td>
                    <td>{{ $tarik->diajukan_pada->format('d/m/Y H:i') }}</td>
                    <td><b>Rp {{ number_format($tarik->nominal, 0, ',', '.') }}</b></td>
                    <td>
                        <span class="pill {{ in_array($tarik->status, ['dicairkan', 'disetujui']) ? 'ok' : ($tarik->status === 'menunggu' ? '' : 'no') }}">
                            {{ ucfirst($tarik->status) }}
                        </span>
                        @if($tarik->status === 'ditolak' && $tarik->alasan_penolakan)
                            <small style="color:var(--mu);display:block">Alasan: {{ $tarik->alasan_penolakan }}</small>
                        @elseif($tarik->status === 'disetujui')
                            <small style="color:var(--em);display:block">Silakan temui pengurus untuk serah terima tunai</small>
                        @endif
                    </td>
                    <td>
                        @if($tarik->status === 'menunggu')
                            <form method="POST" action="{{ route('nasabah.penarikan.batalkan', $tarik) }}" style="margin:0">
                                @csrf
                                <button class="btn o sm" type="submit" onclick="return confirm('Apakah Ibu yakin ingin membatalkan pengajuan penarikan ini?')">
                                    Batalkan
                                </button>
                            </form>
                        @else
                            <span style="color:var(--mu);font-size:12px">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada riwayat pengajuan penarikan dana.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">
    {{ $penarikanList->links() }}
</div>
@endsection
