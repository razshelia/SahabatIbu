@extends('layouts.app', ['title' => 'Penarikan Dana · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Penarikan Dana'])

@section('content')
<small class="kick">Penarikan dana</small>
<h1>Ajukan <em>penarikan</em></h1>
<p class="lead">Pilih jumlah dan cara menerima dana: tunai atau transfer.</p>
@if($nasabah->kategori === 'umum' && $batasMaksimalUmum > 0)
    <p class="lead">Batas maksimal penarikan untuk nasabah umum adalah <b>Rp {{ number_format($batasMaksimalUmum, 2, ',', '.') }}</b> per pengajuan.</p>
@endif

<x-alat tool="capit" plat="Penarikan dana">
    <x-slot:layar>
        <div class="baris">
            <div>
                <small>Saldo tersedia</small>
                <b class="angka">Rp {{ number_format($nasabah->saldo_tersedia, 0, ',', '.') }}</b>
            </div>
            <div class="kanan">
                <small>Sedang ditahan</small>
                <b class="angka kecil kuning">Rp {{ number_format($nasabah->saldo_ditahan, 0, ',', '.') }}</b>
            </div>
        </div>
        <p class="baca">Jumlah pengajuan: <b data-ikut-rp="nominal" data-kosong="belum diisi">belum diisi</b> · <span id="preview-metode">Tunai</span></p>
    </x-slot:layar>

    <form method="POST" action="{{ route('nasabah.penarikan.store') }}" id="form-tarik" style="display:grid;gap:16px">
        @csrf
        @include('partials.rupiah-input', ['id' => 'nominal', 'name' => 'nominal', 'label' => 'Berapa yang mau diambil?', 'step' => 5000, 'max' => (int) $nasabah->saldo_tersedia, 'maxLabel' => 'Semua saldo', 'draf' => 'capit'])
        <div data-validation-group="metode">
            <label style="display:block;margin-bottom:8px;font-weight:700">Cara menerima dana</label>
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:10px">
                <label style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:14px;border:1px solid var(--line);background:rgba(255,255,255,0.04);cursor:pointer">
                    <input type="radio" name="metode" value="tunai" id="opt-tunai" required style="width:auto;margin:0" {{ old('metode', 'tunai') === 'tunai' ? 'checked' : '' }} onchange="toggleMetode()">
                    <div>
                        <b style="display:block">💵 Tunai</b>
                        <small style="opacity:0.7">Terima uang langsung dari pengurus</small>
                    </div>
                </label>
                <label style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:14px;border:1px solid var(--line);background:rgba(255,255,255,0.04);cursor:pointer">
                    <input type="radio" name="metode" value="transfer" id="opt-transfer" style="width:auto;margin:0" {{ old('metode') === 'transfer' ? 'checked' : '' }} onchange="toggleMetode()">
                    <div>
                        <b style="display:block">📱 Transfer Bank / E-Wallet</b>
                        <small style="opacity:0.7">Dikirim ke rekening atau e-wallet</small>
                    </div>
                </label>
            </div>
            @error('metode')<p class="err" data-validation-error-for="metode">{{ $message }}</p>@enderror
        </div>

        <div id="kolom-transfer" style="display:{{ old('metode') === 'transfer' ? 'grid' : 'none' }};gap:12px;padding:16px;border-radius:14px;background:rgba(255,255,255,0.03);border:1px dashed var(--line)">
            <div>
                <label for="nama_bank" style="display:block;margin-bottom:6px;font-weight:700">Bank atau e-wallet tujuan</label>
                <input list="list-bank" id="nama_bank" name="nama_bank" type="text" value="{{ old('nama_bank') }}" maxlength="100" placeholder="Contoh: BCA / BRI / Mandiri / GoPay / DANA / OVO" style="width:100%">
                <datalist id="list-bank">
                    <option value="BCA">
                    <option value="BRI">
                    <option value="Mandiri">
                    <option value="BNI">
                    <option value="BSI">
                    <option value="GoPay">
                    <option value="OVO">
                    <option value="DANA">
                    <option value="ShopeePay">
                </datalist>
                @error('nama_bank')<p class="err" data-validation-error-for="nama_bank">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="nomor_rekening" style="display:block;margin-bottom:6px;font-weight:700">Nomor rekening atau nomor HP e-wallet</label>
                <input id="nomor_rekening" name="nomor_rekening" type="text" inputmode="numeric" value="{{ old('nomor_rekening') }}" maxlength="50" placeholder="Contoh: 1234567890 atau 08123456789" style="width:100%">
                @error('nomor_rekening')<p class="err" data-validation-error-for="nomor_rekening">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="nama_pemilik" style="display:block;margin-bottom:6px;font-weight:700">Nama pemilik rekening atau akun</label>
                <input id="nama_pemilik" name="nama_pemilik" type="text" value="{{ old('nama_pemilik') }}" maxlength="100" placeholder="Contoh: Siti Aminah" style="width:100%">
                @error('nama_pemilik')<p class="err" data-validation-error-for="nama_pemilik">{{ $message }}</p>@enderror
            </div>

            <div class="privacy-note" role="note">
                <span class="privacy-note-icon" aria-hidden="true">i</span>
                <span>Data rekening atau e-wallet hanya digunakan untuk pengajuan ini. Data tersebut tidak disimpan di profil Ibu.</span>
            </div>
        </div>

        <div class="deret">
            <button class="key r besar" type="submit">Ajukan penarikan</button>
        </div>
    </form>

    <x-slot:nampan>
        <p>Setelah diajukan, statusnya <b>Menunggu</b> sampai diperiksa pengurus. Jika memilih <b>Tunai</b>, temui pengurus setelah disetujui. Jika memilih <b>Transfer</b>, pengurus akan mengirim dana ke rekening atau e-wallet yang Ibu cantumkan.</p>
    </x-slot:nampan>
</x-alat>

<script>
    function toggleMetode() {
        const isTransfer = document.getElementById('opt-transfer').checked;
        const divTransfer = document.getElementById('kolom-transfer');
        const prev = document.getElementById('preview-metode');
        divTransfer.style.display = isTransfer ? 'grid' : 'none';
        if (prev) prev.textContent = isTransfer ? 'Transfer' : 'Tunai';

        const bankInput = document.getElementById('nama_bank');
        const rekInput = document.getElementById('nomor_rekening');
        const namaInput = document.getElementById('nama_pemilik');
        if (isTransfer) {
            bankInput.setAttribute('required', 'required');
            rekInput.setAttribute('required', 'required');
            namaInput.setAttribute('required', 'required');
        } else {
            bankInput.removeAttribute('required');
            rekInput.removeAttribute('required');
            namaInput.removeAttribute('required');
        }
    }
    window.onAppPageReady(toggleMetode);
</script>

<h2>Riwayat pengajuan penarikan</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Diajukan</th>
                <th>Metode</th>
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
                    <td>
                        @if($tarik->metode === 'transfer')
                            <span class="pill ok">Transfer</span>
                            <small style="display:block;margin-top:4px">
                                <b>{{ $tarik->nama_bank }}</b> · {{ $tarik->nomor_rekening_masked }}<br>
                                <span style="opacity:0.75">a.n. {{ $tarik->nama_pemilik }}</span>
                            </small>
                        @else
                            <span class="pill">Tunai</span>
                        @endif
                    </td>
                    <td><b>Rp {{ number_format($tarik->nominal, 0, ',', '.') }}</b></td>
                    <td>
                        <span class="pill {{ in_array($tarik->status, ['dicairkan', 'disetujui']) ? 'ok' : ($tarik->status === 'menunggu' ? '' : 'no') }}">
                            {{ ucfirst($tarik->status) }}
                        </span>
                        @if($tarik->status === 'ditolak' && $tarik->alasan_penolakan)
                            <small style="color:var(--mu);display:block">Alasan: {{ $tarik->alasan_penolakan }}</small>
                        @elseif($tarik->status === 'disetujui')
                            @if($tarik->metode === 'transfer')
                                <small style="color:var(--em);display:block">Disetujui, sedang diproses untuk transfer</small>
                            @else
                                <small style="color:var(--em);display:block">Silakan temui pengurus untuk menerima uang</small>
                            @endif
                        @elseif($tarik->status === 'dicairkan' && $tarik->catatan_referensi)
                            <small style="color:var(--green);display:block">Referensi: {{ $tarik->catatan_referensi }}</small>
                        @endif
                    </td>
                    <td>
                        @if($tarik->status === 'menunggu')
                            <form method="POST" action="{{ route('nasabah.penarikan.batalkan', $tarik) }}" style="margin:0">
                                @csrf
                                <button class="btn o sm" type="submit" onclick="return confirm('Batalkan pengajuan penarikan ini?')">
                                    Batalkan
                                </button>
                            </form>
                        @else
                            <span style="color:var(--mu);font-size:12px">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Belum ada pengajuan penarikan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">
    {{ $penarikanList->links() }}
</div>
@endsection
