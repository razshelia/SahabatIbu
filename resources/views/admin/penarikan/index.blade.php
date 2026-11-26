@extends('layouts.app', ['title' => 'Kelola Penarikan Dana · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Kelola Penarikan Dana'])

@section('content')
<small class="kick">Penarikan dana</small>
<h1>Pengajuan <em>penarikan dana</em></h1>
<p class="lead">Periksa pengajuan, lalu setujui atau tolak. Setelah dana diterima nasabah, tandai pengajuan sebagai dicairkan.</p>

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
                <small>Status yang dipilih</small>
                <b class="angka kecil kuning">{{ $statusTombol[(string) request('status')] ?? 'Semua' }}</b>
            </div>
        </div>
    </x-slot:layar>

    <div>
        <p style="font-weight:800;font-size:17px;margin:0 0 10px">Pilih status untuk menyaring daftar</p>
        <div class="deret" role="group" aria-label="Saring menurut status">
            @foreach($statusTombol as $nilai => $nama)
                <a href="{{ route('admin.penarikan.index', $nilai === '' ? [] : ['status' => $nilai]) }}"
                   class="key sm {{ (string) request('status') === (string) $nilai ? 'on' : 'p' }}"
                   @if((string) request('status') === (string) $nilai) aria-current="true" @endif>{{ $nama }}</a>
            @endforeach
        </div>
    </div>

    <x-slot:nampan>
        <p>Setujui pengajuan jika datanya sesuai. Setelah uang tunai diserahkan atau transfer dikirim, tandai pengajuan sebagai dicairkan.</p>
    </x-slot:nampan>
</x-alat>

<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Diajukan</th>
                <th>Nasabah</th>
                <th>Cara dan tujuan</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Diproses oleh</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penarikanList as $penarikan)
                <tr>
                    <td><b>{{ $penarikan->kode }}</b></td>
                    <td>{{ $penarikan->diajukan_pada->format('d/m/Y H:i') }}</td>
                    <td>{{ $penarikan->nasabah->user->name ?? '-' }}</td>
                    <td>
                        @if($penarikan->metode === 'transfer')
                            <span class="pill ok">Transfer</span>
                            <div style="margin-top:6px;font-size:13px;line-height:1.4">
                                <b>{{ $penarikan->nama_bank }}</b><br>
                                <span style="font-family:monospace;font-size:14px;background:rgba(255,255,255,0.08);padding:2px 6px;border-radius:6px;display:inline-block;margin:2px 0">{{ $penarikan->nomor_rekening }}</span>
                                <button type="button" class="btn sm" style="min-height:24px;padding:2px 8px;font-size:11px;margin-left:4px" onclick="salinTeks('{{ addslashes($penarikan->nomor_rekening) }}', this)">
                                    📋 Salin
                                </button><br>
                                <small style="opacity:0.8">a.n. {{ $penarikan->nama_pemilik }}</small>
                            </div>
                        @else
                            <span class="pill">Tunai</span>
                        @endif
                    </td>
                    <td>
                        <b>Rp {{ number_format($penarikan->nominal, 0, ',', '.') }}</b>
                        <button type="button" class="btn sm" style="min-height:22px;padding:2px 6px;font-size:11px;display:block;margin-top:4px" onclick="salinTeks('{{ (int)$penarikan->nominal }}', this)">
                            📋 Salin jumlah
                        </button>
                    </td>
                    <td>
                        <span class="pill {{ in_array($penarikan->status, ['dicairkan', 'disetujui']) ? 'ok' : ($penarikan->status === 'menunggu' ? '' : 'no') }}">
                            {{ ucfirst($penarikan->status) }}
                        </span>
                        @if($penarikan->alasan_penolakan)
                            <small style="color:var(--mu);display:block">Alasan: {{ $penarikan->alasan_penolakan }}</small>
                        @elseif($penarikan->status === 'dicairkan' && $penarikan->catatan_referensi)
                            <small style="color:var(--green);display:block">Referensi: {{ $penarikan->catatan_referensi }}</small>
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

                                <button id="tolak-penarikan-{{ $penarikan->id }}" class="btn o sm" type="button" data-id="{{ $penarikan->id }}" data-kode="{{ $penarikan->kode }}" data-nama="{{ $penarikan->nasabah->user->name ?? '' }}" data-nominal="Rp {{ number_format($penarikan->nominal, 0, ',', '.') }}" onclick="bukaModalTolakDariTombol(this)">
                                    Tolak
                                </button>
                            @elseif($penarikan->status === 'disetujui')
                                <button id="cairkan-penarikan-{{ $penarikan->id }}" class="btn sm" type="button" data-id="{{ $penarikan->id }}" data-kode="{{ $penarikan->kode }}" data-nama="{{ $penarikan->nasabah->user->name ?? '' }}" data-nominal="Rp {{ number_format($penarikan->nominal, 0, ',', '.') }}" data-nominal-raw="{{ (int)$penarikan->nominal }}" data-metode="{{ $penarikan->metode }}" data-bank="{{ $penarikan->nama_bank ?? '' }}" data-rekening="{{ $penarikan->nomor_rekening ?? '' }}" data-pemilik="{{ $penarikan->nama_pemilik ?? '' }}" onclick="bukaModalCairkanDariTombol(this)">
                                    {{ $penarikan->metode === 'transfer' ? 'Konfirmasi transfer' : 'Konfirmasi pembayaran tunai' }}
                                </button>
                            @else
                                <span style="color:var(--mu);font-size:12px">Selesai</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">Belum ada pengajuan penarikan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">
    {{ $penarikanList->links() }}
</div>

<dialog id="modal-cairkan-penarikan" class="dlg-alat">
    <x-alat tool="capit" plat="Konfirmasi Pencairan">
        <x-slot:layar>
            <small>Pengajuan yang dicairkan</small>
            <b class="angka kecil kuning" id="ket-cairkan-penarikan">-</b>
        </x-slot:layar>
        <form method="POST" id="form-cairkan-penarikan" style="display:grid;gap:16px">
            @csrf
            <input type="hidden" name="cairkan_penarikan_id" id="cairkan-penarikan-id" value="{{ old('cairkan_penarikan_id') }}">
            <div id="cairkan-info-transfer" style="display:none;padding:12px;border-radius:12px;background:rgba(255,255,255,0.04);border:1px solid var(--line)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                    <span style="opacity:0.75">Tujuan transfer:</span>
                    <span class="pill ok" style="font-size:11px">Transfer</span>
                </div>
                <div style="margin-bottom:8px">
                    <b id="cairkan-bank">-</b> · <span id="cairkan-pemilik">-</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                    <code id="cairkan-rek" style="font-size:15px;font-weight:700;background:rgba(255,255,255,0.1);padding:4px 8px;border-radius:6px">-</code>
                    <button type="button" class="btn sm" style="min-height:26px;padding:2px 8px;font-size:12px" onclick="salinTeks(document.getElementById('cairkan-rek').textContent, this)">
                        📋 Salin nomor rekening
                    </button>
                </div>
                <div style="display:flex;align-items:center;gap:8px">
                    <span style="opacity:0.75">Nominal:</span>
                    <b id="cairkan-nom">-</b>
                    <button type="button" class="btn sm" style="min-height:26px;padding:2px 8px;font-size:12px" id="btn-salin-nom-modal">
                        📋 Salin jumlah
                    </button>
                </div>
            </div>

            <div id="cairkan-info-tunai" style="display:none;padding:12px;border-radius:12px;background:rgba(255,255,255,0.04);border:1px solid var(--line)">
                <span class="pill">Tunai</span>
                <p style="margin:8px 0 0">Pastikan uang sudah diserahkan kepada nasabah.</p>
            </div>

            <label for="catatan_referensi">
                Nomor bukti atau catatan (opsional)
                <input id="catatan_referensi" name="catatan_referensi" type="text" maxlength="255" value="{{ old('catatan_referensi') }}" placeholder="Contoh: Nomor referensi transfer atau lokasi serah terima">
                @error('catatan_referensi')<span class="err" data-validation-error-for="catatan_referensi">{{ $message }}</span>@enderror
            </label>

            <div class="deret">
                <button class="key g besar" type="submit">Tandai sudah dicairkan</button>
                <button class="key p" type="button" onclick="document.getElementById('modal-cairkan-penarikan').close()">Batal</button>
            </div>
        </form>
    </x-alat>
</dialog>

<dialog id="modal-tolak-penarikan" class="dlg-alat">
    <x-alat tool="capit" plat="Tolak Pengajuan">
        <x-slot:layar>
            <small>Pengajuan yang ditolak</small>
            <b class="angka kecil kuning" id="ket-tolak-penarikan">-</b>
        </x-slot:layar>
        <form method="POST" id="form-tolak-penarikan" style="display:grid;gap:16px">
            @csrf
            <input type="hidden" name="tolak_penarikan_id" id="tolak-penarikan-id" value="{{ old('tolak_penarikan_id') }}">
            <small>Jika ditolak, dana yang ditahan akan kembali ke saldo tersedia nasabah.</small>
            <label for="alasan_penolakan">
                Alasan penolakan (wajib)
                <input id="alasan_penolakan" name="alasan_penolakan" type="text" maxlength="255" value="{{ old('alasan_penolakan') }}" required placeholder="Contoh: Kas fisik belum tersedia hari ini">
                @error('alasan_penolakan')<span class="err" data-validation-error-for="alasan_penolakan">{{ $message }}</span>@enderror
            </label>
            <div class="deret">
                <button class="key r besar" type="submit">Tolak pengajuan</button>
                <button class="key p" type="button" onclick="document.getElementById('modal-tolak-penarikan').close()">Batal</button>
            </div>
        </form>
    </x-alat>
</dialog>

<script>
    function salinTeks(teks, btn) {
        if (!teks) return;
        navigator.clipboard.writeText(teks).then(() => {
            const orig = btn.textContent;
            btn.textContent = '✓ Tersalin';
            btn.style.borderColor = 'var(--green)';
            setTimeout(() => {
                btn.textContent = orig;
                btn.style.borderColor = '';
            }, 2000);
        }).catch(() => {
            prompt('Salin teks ini:', teks);
        });
    }

    function bukaModalCairkan(id, kode, nama, nominalFormatted, rawNominal, metode, bank, rek, pemilik) {
        const modal = document.getElementById('modal-cairkan-penarikan');
        const form = document.getElementById('form-cairkan-penarikan');
        document.getElementById('cairkan-penarikan-id').value = id;
        document.getElementById('ket-cairkan-penarikan').textContent = `${kode} · ${nama} · ${nominalFormatted}`;
        form.action = "{{ url('admin/penarikan') }}/" + id + "/cairkan";

        const infoTransfer = document.getElementById('cairkan-info-transfer');
        const infoTunai = document.getElementById('cairkan-info-tunai');
        const btnSalinNom = document.getElementById('btn-salin-nom-modal');

        if (metode === 'transfer') {
            infoTransfer.style.display = 'block';
            infoTunai.style.display = 'none';
            document.getElementById('cairkan-bank').textContent = bank;
            document.getElementById('cairkan-rek').textContent = rek;
            document.getElementById('cairkan-pemilik').textContent = 'a.n. ' + pemilik;
            document.getElementById('cairkan-nom').textContent = nominalFormatted;
            btnSalinNom.onclick = function() { salinTeks(rawNominal, this); };
        } else {
            infoTransfer.style.display = 'none';
            infoTunai.style.display = 'block';
        }

        document.getElementById('catatan_referensi').value = '';
        modal.showModal();
    }

    function bukaModalCairkanDariTombol(button) {
        bukaModalCairkan(
            Number(button.dataset.id),
            button.dataset.kode,
            button.dataset.nama,
            button.dataset.nominal,
            button.dataset.nominalRaw,
            button.dataset.metode,
            button.dataset.bank,
            button.dataset.rekening,
            button.dataset.pemilik
        );
    }

    function bukaModalTolak(id, kode, nama, nominal) {
        const modal = document.getElementById('modal-tolak-penarikan');
        const form = document.getElementById('form-tolak-penarikan');
        document.getElementById('tolak-penarikan-id').value = id;
        document.getElementById('ket-tolak-penarikan').textContent = `${kode} · ${nama} · ${nominal}`;
        form.action = "{{ url('admin/penarikan') }}/" + id + "/tolak";
        modal.showModal();
    }

    function bukaModalTolakDariTombol(button) {
        bukaModalTolak(Number(button.dataset.id), button.dataset.kode, button.dataset.nama, button.dataset.nominal);
    }
</script>
@if($errors->has('catatan_referensi') && old('cairkan_penarikan_id'))
    <script>
        window.onAppPageReady(() => {
            const id = Number(document.getElementById('cairkan-penarikan-id').value);
            const button = Number.isInteger(id) && id > 0 ? document.getElementById('cairkan-penarikan-' + id) : null;
            if (!button) return;
            bukaModalCairkan(id, button.dataset.kode, button.dataset.nama, button.dataset.nominal, button.dataset.nominalRaw, button.dataset.metode, button.dataset.bank, button.dataset.rekening, button.dataset.pemilik);
            document.getElementById('catatan_referensi').value = @json(old('catatan_referensi'));
        });
    </script>
@elseif($errors->has('alasan_penolakan') && old('tolak_penarikan_id'))
    <script>
        window.onAppPageReady(() => {
            const id = Number(document.getElementById('tolak-penarikan-id').value);
            const button = Number.isInteger(id) && id > 0 ? document.getElementById('tolak-penarikan-' + id) : null;
            if (!button) return;
            bukaModalTolak(id, button.dataset.kode, button.dataset.nama, button.dataset.nominal);
            document.getElementById('alasan_penolakan').value = @json(old('alasan_penolakan'));
        });
    </script>
@endif
@endsection
