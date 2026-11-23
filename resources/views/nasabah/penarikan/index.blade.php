@extends('layouts.app', ['title' => 'Penarikan Dana · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Penarikan Dana'])

@section('content')
<small class="kick">Penarikan dana</small>
<h1>Ajukan <em>ambil uang</em></h1>
<p class="lead">Isi jumlah uang yang ingin Ibu ambil, lalu tekan tombol merah. Uangnya ditahan dulu sampai pengurus menyiapkan tunai.</p>

<x-alat tool="capit" plat="Ambil Uang">
    <x-slot:layar>
        <div class="baris">
            <div>
                <small>Uang bisa diambil</small>
                <b class="angka">Rp {{ number_format($nasabah->saldo_tersedia, 0, ',', '.') }}</b>
            </div>
            <div class="kanan">
                <small>Sedang ditahan</small>
                <b class="angka kecil kuning">Rp {{ number_format($nasabah->saldo_ditahan, 0, ',', '.') }}</b>
            </div>
        </div>
        <p class="baca">Akan diajukan: <b data-ikut-rp="nominal" data-kosong="belum diisi">belum diisi</b></p>
    </x-slot:layar>

    <form method="POST" action="{{ route('nasabah.penarikan.store') }}" id="form-tarik" style="display:grid;gap:16px">
        @csrf
        @include('partials.rupiah-input', ['id' => 'nominal', 'name' => 'nominal', 'label' => 'Berapa yang mau diambil?', 'step' => 5000, 'max' => (int) $nasabah->saldo_tersedia, 'maxLabel' => 'Semua saldo', 'draf' => 'capit'])
        @error('nominal')<p class="err">{{ $message }}</p>@enderror
        <div class="deret">
            <button class="key r besar" type="submit">Ambil uang</button>
        </div>
    </form>

    <x-slot:nampan>
        <p>Setelah diajukan, status berubah menjadi Menunggu. Jika disetujui, temui pengurus untuk menerima uang tunai.</p>
    </x-slot:nampan>
</x-alat>

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
