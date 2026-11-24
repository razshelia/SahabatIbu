@extends('layouts.app', ['title' => 'Catat Setoran · Bank Sampah Sahabat Ibu', 'dataView' => 'timbang', 'pageTitle' => 'Catat Setoran Baru'])

@section('content')
<small class="kick">Setoran</small>
<h1>Catat <em>setoran sampah</em></h1>
<p class="lead">Pilih nasabah dan masukkan berat setiap jenis sampah. Perkiraan nilai setoran akan dihitung otomatis.</p>

<form method="POST" action="{{ route('admin.setoran.store') }}" novalidate id="form-setoran" data-require-one-positive="items">
    @csrf

    <x-alat tool="timbang" plat="Timbang Sampah">
        <x-slot:layar>
            <div class="baris">
                <div>
                    <small>Berat total</small>
                    <b class="angka" id="lcd-kg">0,00 kg</b>
                </div>
                <div class="kanan">
                    <small>Nasabah</small>
                    <b class="angka kecil kuning" id="lcd-nasabah">Belum dipilih</b>
                </div>
            </div>
            <p class="baca">Perkiraan nilai setoran: <b id="lcd-rp">Rp 0</b></p>
        </x-slot:layar>

        <label for="nasabah_id">
            Pilih nasabah aktif
            <select id="nasabah_id" name="nasabah_id" required autofocus>
                <option value="">-- Pilih nasabah --</option>
                @foreach($nasabahList as $nasabah)
                    <option value="{{ $nasabah->id }}" data-nama="{{ $nasabah->user->name }}" {{ old('nasabah_id') == $nasabah->id ? 'selected' : '' }}>
                        {{ $nasabah->nomor_nasabah }} · {{ $nasabah->user->name }} (Saldo: Rp {{ number_format($nasabah->saldo, 0, ',', '.') }})
                    </option>
                @endforeach
            </select>
            @error('nasabah_id')<span class="err" data-validation-error-for="nasabah_id">{{ $message }}</span>@enderror
        </label>

        <div>
            <p style="font-weight:800;font-size:17px;margin:0 0 4px">Masukkan berat sampah</p>
            <small>Biarkan kosong untuk jenis yang tidak disetor. Gunakan tombol + atau − untuk mengubah berat sebesar 0,25 kg, atau ketik angka dengan koma atau titik (maksimal 2 angka di belakang koma).</small>
        </div>

        <div class="baki" data-validation-group="items">
            @foreach($jenisList as $index => $jenis)
                <div class="baki-baris">
                    <div class="baki-nama">
                        <label for="berat-{{ $index }}">{{ $jenis->nama }}</label>
                        <small>@ Rp {{ number_format($jenis->harga_saat_ini, 0, ',', '.') }} / {{ $jenis->satuan }}</small>
                    </div>
                    <div class="langkah">
                        <button type="button" class="key p sm" data-langkah="-0.25" aria-label="Kurangi 0,25 {{ $jenis->satuan }} {{ $jenis->nama }}">−</button>
                        <input type="hidden" name="items[{{ $index }}][jenis_sampah_id]" value="{{ $jenis->id }}">
                        <input type="text"
                               id="berat-{{ $index }}"
                               name="items[{{ $index }}][berat_kg]"
                               class="input-berat"
                               data-optional-positive
                               data-harga="{{ $jenis->harga_saat_ini }}"
                               inputmode="decimal"
                               pattern="[0-9]+([.,][0-9]{1,2})?"
                               data-validation-pattern-message="Masukkan angka dengan maksimal dua angka di belakang koma."
                               autocomplete="off"
                               placeholder="0,00"
                               value="{{ old("items.{$index}.berat_kg") }}">
                        @error("items.{$index}.berat_kg")<span class="err" data-validation-error-for="items.{{ $index }}.berat_kg">{{ $message }}</span>@enderror
                        <button type="button" class="key g sm" data-langkah="0.25" aria-label="Tambah 0,25 {{ $jenis->satuan }} {{ $jenis->nama }}">+</button>
                    </div>
                </div>
            @endforeach
        </div>
        @error('items')<span class="err" data-validation-error-for="items">{{ $message }}</span>@enderror

        <div class="deret">
            <button class="key g besar" type="submit">Simpan setoran</button>
            <a href="{{ route('admin.setoran.index') }}" class="key p">Batal</a>
        </div>

        <x-slot:nampan>
            <p>Setelah setoran disimpan, nilainya ditambahkan ke saldo nasabah. Rincian transaksi tersedia di struk.</p>
        </x-slot:nampan>
    </x-alat>
</form>

@push('scripts')
<script>
    window.onAppPageReady(() => {
        const form = document.getElementById('form-setoran');
        const inputs = form.querySelectorAll('.input-berat');
        const pilih = document.getElementById('nasabah_id');
        const lcdKg = document.getElementById('lcd-kg');
        const lcdRp = document.getElementById('lcd-rp');
        const lcdNsb = document.getElementById('lcd-nasabah');

        const ke = (v) => {
            const n = parseFloat(String(v || '').replace(',', '.').trim());
            return isNaN(n) || n < 0 ? 0 : n;
        };

        function hitungTotal() {
            let totalNilai = 0;
            let totalBerat = 0;
            inputs.forEach(inp => {
                const berat = ke(inp.value);
                const harga = parseFloat(inp.dataset.harga || 0);
                if (berat > 0) {
                    totalBerat += berat;
                    totalNilai += Math.round(berat * harga);
                }
            });
            const nama = pilih.selectedOptions[0] && pilih.value ? pilih.selectedOptions[0].dataset.nama : '';
            lcdKg.textContent = totalBerat.toFixed(2).replace('.', ',') + ' kg';
            lcdRp.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalNilai);
            lcdNsb.textContent = nama || 'Belum dipilih';
            if (window.ALAT) {
                window.ALAT.set('timbang', { kg: totalBerat, v: totalNilai, nama: nama });
            }
        }

        inputs.forEach(inp => {
            // hanya angka, koma, titik: tanda minus dan huruf tidak diterima
            inp.addEventListener('input', () => {
                const bersih = inp.value.replace(/[^0-9.,]/g, '');
                if (bersih !== inp.value) { inp.value = bersih; }
                hitungTotal();
            });
        });
        pilih.addEventListener('change', hitungTotal);

        // tombol + dan − bergeser 0,25 dan tidak pernah di bawah 0
        form.querySelectorAll('[data-langkah]').forEach(tombol => {
            tombol.addEventListener('click', () => {
                const inp = tombol.closest('.langkah').querySelector('.input-berat');
                const baru = Math.max(0, Math.round((ke(inp.value) + parseFloat(tombol.dataset.langkah)) * 100) / 100);
                inp.value = baru > 0 ? baru.toFixed(2).replace('.', ',') : '';
                hitungTotal();
                inp.focus();
            });
        });

        hitungTotal();

        // Ganti koma ke titik saat submit
        form.addEventListener('submit', function () {
            inputs.forEach(inp => {
                if (inp.value) {
                    inp.value = inp.value.replace(',', '.').trim();
                }
            });
        });
    });
</script>
@endpush
@endsection
