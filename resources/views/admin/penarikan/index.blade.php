@extends('layouts.app', ['title' => 'Kelola Penarikan Dana · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Kelola Penarikan Dana'])

@section('content')
<small class="kick">Penarikan dana</small>
<h1>Pengajuan <em>penarikan uang</em></h1>
<p class="lead">Kelola persetujuan dan pencairan dana tunai kepada nasabah (BR-07, BR-08, BR-09).</p>

@php
    $statusTombol = [
        '' => 'Semua',
        'menunggu' => 'Menunggu',
        'disetujui' => 'Disetujui',
        'dicairkan' => 'Dicairkan',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
    ];
@endphp
<x-alat tool="capit" plat="Penarikan">
    <x-slot:layar>
        <div class="baris">
            <div>
                <small>Pengajuan yang tampil</small>
                <b class="angka">{{ $penarikanList->total() }}</b>
            </div>
            <div class="kanan">
                <small>Saring status</small>
                <b class="angka kecil kuning">{{ $statusTombol[(string) request('status')] ?? 'Semua' }}</b>
            </div>
        </div>
    </x-slot:layar>

    <div>
        <p style="font-weight:800;font-size:17px;margin:0 0 10px">Tekan status untuk menyaring daftar</p>
        <div class="deret" role="group" aria-label="Saring menurut status">
            @foreach($statusTombol as $nilai => $nama)
                <a href="{{ route('admin.penarikan.index', $nilai === '' ? [] : ['status' => $nilai]) }}"
                   class="key sm {{ (string) request('status') === (string) $nilai ? 'on' : 'p' }}"
                   @if((string) request('status') === (string) $nilai) aria-current="true" @endif>{{ $nama }}</a>
            @endforeach
        </div>
    </div>

    <x-slot:nampan>
        <p>Setujui dulu pengajuan yang masuk. Setelah uang tunai diserahkan langsung ke nasabah, tekan Cairkan Tunai.</p>
    </x-slot:nampan>
</x-alat>

<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Waktu Pengajuan</th>
                <th>Nasabah</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Diproses Oleh</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penarikanList as $penarikan)
                <tr>
                    <td><b>{{ $penarikan->kode }}</b></td>
                    <td>{{ $penarikan->diajukan_pada->format('d/m/Y H:i') }}</td>
                    <td>{{ $penarikan->nasabah->user->name ?? '-' }}</td>
                    <td><b>Rp {{ number_format($penarikan->nominal, 0, ',', '.') }}</b></td>
                    <td>
                        <span class="pill {{ in_array($penarikan->status, ['dicairkan', 'disetujui']) ? 'ok' : ($penarikan->status === 'menunggu' ? '' : 'no') }}">
                            {{ ucfirst($penarikan->status) }}
                        </span>
                        @if($penarikan->alasan_penolakan)
                            <small style="color:var(--mu);display:block">Alasan: {{ $penarikan->alasan_penolakan }}</small>
                        @endif
                    </td>
                    <td>{{ $penarikan->pemroses->name ?? '-' }}</td>
                    <td>
                        <div class="row" style="gap:6px">
                            @if($penarikan->status === 'menunggu')
                                <form method="POST" action="{{ route('admin.penarikan.setujui', $penarikan) }}" style="margin:0">
                                    @csrf
                                    <button class="btn sm" type="submit">Setujui</button>
                                </form>

                                <button class="btn o sm" type="button" onclick="bukaModalTolak({{ $penarikan->id }}, '{{ addslashes($penarikan->kode) }}', '{{ addslashes($penarikan->nasabah->user->name ?? '') }}', 'Rp {{ number_format($penarikan->nominal, 0, ',', '.') }}')">
                                    Tolak
                                </button>
                            @elseif($penarikan->status === 'disetujui')
                                <form method="POST" action="{{ route('admin.penarikan.cairkan', $penarikan) }}" style="margin:0">
                                    @csrf
                                    <button class="btn sm" type="submit" onclick="return confirm('Konfirmasi bahwa uang tunai Rp {{ number_format($penarikan->nominal, 0, ',', '.') }} telah diserahkan langsung ke nasabah?')">
                                        Cairkan Tunai
                                    </button>
                                </form>
                            @else
                                <span style="color:var(--mu);font-size:12px">Selesai</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">Belum ada pengajuan penarikan dana.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">
    {{ $penarikanList->links() }}
</div>

<dialog id="modal-tolak-penarikan" class="dlg-alat">
    <x-alat tool="capit" plat="Tolak Pengajuan">
        <x-slot:layar>
            <small>Pengajuan yang ditolak</small>
            <b class="angka kecil kuning" id="ket-tolak-penarikan">-</b>
        </x-slot:layar>
        <form method="POST" id="form-tolak-penarikan" style="display:grid;gap:16px">
            @csrf
            <small>Penolakan mengembalikan saldo yang ditahan ke saldo tersedia nasabah.</small>
            <label for="alasan_penolakan">
                Alasan penolakan (wajib)
                <input id="alasan_penolakan" name="alasan_penolakan" type="text" maxlength="255" required placeholder="Contoh: Kas fisik belum tersedia hari ini">
            </label>
            <div class="deret">
                <button class="key r besar" type="submit">Kirim penolakan</button>
                <button class="key p" type="button" onclick="document.getElementById('modal-tolak-penarikan').close()">Batal</button>
            </div>
        </form>
    </x-alat>
</dialog>

<script>
    function bukaModalTolak(id, kode, nama, nominal) {
        const modal = document.getElementById('modal-tolak-penarikan');
        const form = document.getElementById('form-tolak-penarikan');
        document.getElementById('ket-tolak-penarikan').textContent = `${kode} · ${nama} · ${nominal}`;
        form.action = "{{ url('admin/penarikan') }}/" + id + "/tolak";
        modal.showModal();
    }
</script>
@endsection
