@extends('layouts.app', ['title' => 'Katalog Harga · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Katalog Harga Sampah'])

@section('content')
<small class="kick">Katalog harga</small>
<h1>Harga sampah <em>harian</em></h1>
<p class="lead">Atur pembaruan harga per kg yang berlaku harian (BR-03, BR-04).</p>

<form method="POST" action="{{ route('admin.katalog.harga.store') }}" id="form-harga">
    @csrf
    <x-alat tool="rak" plat="Katalog Harga">
        <x-slot:layar>
            <small id="lcd-jenis">Harga sekarang</small>
            <b class="angka" id="lcd-lama">-</b>
            <p class="baca">Harga baru: <b id="lcd-baru">belum diisi</b> <span id="lcd-selisih"></span></p>
        </x-slot:layar>

        <label for="jenis_sampah_id">
            Jenis sampah
            <select id="jenis_sampah_id" name="jenis_sampah_id" required>
                @foreach($jenisList as $j)
                    <option value="{{ $j->id }}" data-nama="{{ $j->nama }}" data-satuan="{{ $j->satuan }}" data-sekarang="{{ (int) ($j->harga_berlaku->harga_per_kg ?? 0) }}">{{ $j->nama }} ({{ $j->satuan }})</option>
                @endforeach
            </select>
        </label>

        @include('partials.rupiah-input', ['id' => 'harga_per_kg', 'name' => 'harga_per_kg', 'label' => 'Harga baru per kg', 'step' => 500, 'placeholder' => '0'])

        <label for="berlaku_mulai">
            Berlaku mulai
            <input id="berlaku_mulai" name="berlaku_mulai" type="date" required value="{{ date('Y-m-d') }}">
        </label>

        <div class="deret">
            <button class="key g besar" type="submit">Simpan harga</button>
        </div>

        <x-slot:nampan>
            <p>Harga baru berlaku untuk setoran yang ditimbang mulai tanggal di atas. Setoran lama tidak berubah.</p>
        </x-slot:nampan>
    </x-alat>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const pilih = document.getElementById('jenis_sampah_id');
        const lcdJenis = document.getElementById('lcd-jenis');
        const lcdLama = document.getElementById('lcd-lama');
        const lcdBaru = document.getElementById('lcd-baru');
        const lcdSel = document.getElementById('lcd-selisih');
        const inp = document.getElementById('harga_per_kg');
        const rp = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');

        function segarkan() {
            const o = pilih.selectedOptions[0];
            if (!o) { return; }
            const lama = parseInt(o.dataset.sekarang || '0', 10) || 0;
            const baru = parseInt(inp.value || '0', 10) || 0;
            const sat = o.dataset.satuan || 'kg';
            lcdJenis.textContent = 'Harga sekarang · ' + o.dataset.nama;
            lcdLama.textContent = lama > 0 ? rp(lama) + '/' + sat : 'Belum diatur';
            lcdBaru.textContent = baru > 0 ? rp(baru) + '/' + sat : 'belum diisi';
            lcdSel.className = '';
            lcdSel.textContent = '';
            if (baru > 0 && lama > 0 && baru !== lama) {
                lcdSel.textContent = (baru > lama ? '▲ naik ' : '▼ turun ') + rp(Math.abs(baru - lama));
                lcdSel.className = baru > lama ? 'ok' : 'merah';
            }
            if (window.ALAT) { window.ALAT.set('rak', { n: o.dataset.nama, h: baru }); }
        }

        pilih.addEventListener('change', segarkan);
        document.getElementById('form-harga').addEventListener('rupiah', segarkan);
        segarkan();
    });
</script>
@endpush

<h2>Harga yang berlaku saat ini</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Jenis Sampah</th>
                <th>Satuan</th>
                <th>Harga Berlaku</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jenisList as $j)
                <tr>
                    <td><b>{{ $j->nama }}</b></td>
                    <td>{{ $j->satuan }}</td>
                    <td>
                        @if($j->harga_berlaku)
                            Rp {{ number_format($j->harga_berlaku->harga_per_kg, 0, ',', '.') }}/{{ $j->satuan }}
                            <small style="color:var(--mu);display:block">sejak {{ $j->harga_berlaku->berlaku_mulai->format('d/m/Y') }}</small>
                        @else
                            <span style="color:#f87171">Belum diatur</span>
                        @endif
                    </td>
                    <td>
                        <span class="pill {{ $j->aktif ? 'ok' : 'no' }}">
                            {{ $j->aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.katalog.jenis.update', $j) }}" style="display:inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="nama" value="{{ $j->nama }}">
                            <input type="hidden" name="satuan" value="{{ $j->satuan }}">
                            @if(!$j->aktif)
                                <input type="hidden" name="aktif" value="1">
                                <button class="btn o sm" type="submit">Aktifkan</button>
                            @else
                                <button class="btn o sm" type="submit">Nonaktifkan</button>
                            @endif
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada jenis sampah.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<h2>Riwayat perubahan harga</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Jenis Sampah</th>
                <th>Harga per Satuan</th>
                <th>Berlaku Mulai</th>
                <th>Dibuat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayatHargaTerbaru as $rh)
                <tr>
                    <td><b>{{ $rh->jenisSampah->nama ?? '-' }}</b></td>
                    <td>Rp {{ number_format($rh->harga_per_kg, 0, ',', '.') }} / {{ $rh->jenisSampah->satuan ?? 'kg' }}</td>
                    <td>{{ $rh->berlaku_mulai->format('d/m/Y') }}</td>
                    <td>{{ $rh->pembuat->name ?? 'Admin' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada riwayat perubahan harga.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
