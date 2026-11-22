@extends('layouts.app', ['title' => 'Kelola Penarikan Dana · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Kelola Penarikan Dana'])

@section('content')
<small class="kick">Penarikan dana</small>
<h1>Pengajuan <em>penarikan uang</em></h1>
<p class="lead">Kelola persetujuan dan pencairan dana tunai kepada nasabah (BR-07, BR-08, BR-09).</p>

<form class="f frow" method="GET" action="{{ route('admin.penarikan.index') }}" style="margin-bottom:20px">
    <label>
        Status
        <select name="status">
            <option value="">Semua Status</option>
            <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu Persetujuan</option>
            <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui (Siap Dicairkan)</option>
            <option value="dicairkan" {{ request('status') === 'dicairkan' ? 'selected' : '' }}>Dicairkan (Selesai)</option>
            <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
    </label>
    <button class="btn sm" type="submit">Filter</button>
    @if(request('status'))
        <a href="{{ route('admin.penarikan.index') }}" class="btn o sm" style="text-decoration:none">Reset</a>
    @endif
</form>

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

<dialog id="modal-tolak-penarikan">
    <form method="POST" id="form-tolak-penarikan" class="f" style="min-width:320px">
        @csrf
        <h3>Tolak Pengajuan Penarikan</h3>
        <p class="lead" id="ket-tolak-penarikan" style="margin-bottom:12px"></p>
        <p style="font-size:12px;color:var(--mu);margin-bottom:12px">
            Penolakan akan mengembalikan saldo yang ditahan ke saldo tersedia nasabah.
        </p>

        <label for="alasan_penolakan">
            Alasan penolakan (wajib)
            <input id="alasan_penolakan" name="alasan_penolakan" type="text" maxlength="255" required placeholder="Contoh: Kas fisik belum tersedia hari ini">
        </label>

        <div class="row" style="margin-top:14px;gap:8px">
            <button class="btn" type="submit" style="background:#dc2626;border-color:#b91c1c">Kirim Penolakan</button>
            <button class="btn o" type="button" onclick="document.getElementById('modal-tolak-penarikan').close()">Batal</button>
        </div>
    </form>
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
