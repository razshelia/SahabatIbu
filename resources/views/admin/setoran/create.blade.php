@extends('layouts.app', ['title' => 'Catat Setoran · Bank Sampah Sahabat Ibu', 'dataView' => 'timbang', 'pageTitle' => 'Catat Setoran Baru'])

@section('content')
<small class="kick">Setoran</small>
<h1>Catat <em>setoran sampah</em></h1>
<p class="lead">Timbang dan masukkan berat sampah yang disetorkan oleh nasabah (BR-01, BR-05).</p>

<form class="f" method="POST" action="{{ route('admin.setoran.store') }}" style="max-width:680px" novalidate id="form-setoran">
    @csrf

    <label for="nasabah_id">
        Pilih Nasabah Aktif
        <select id="nasabah_id" name="nasabah_id" required autofocus>
            <option value="">-- Pilih Nasabah --</option>
            @foreach($nasabahList as $nasabah)
                <option value="{{ $nasabah->id }}" {{ old('nasabah_id') == $nasabah->id ? 'selected' : '' }}>
                    {{ $nasabah->nomor_nasabah }} · {{ $nasabah->user->name }} (Saldo: Rp {{ number_format($nasabah->saldo, 0, ',', '.') }})
                </option>
            @endforeach
        </select>
        @error('nasabah_id')<span class="err" style="display:block">{{ $message }}</span>@enderror
    </label>

    <h2 style="margin-top:20px;font-size:16px">Timbangan Sampah (kg)</h2>
    <p style="font-size:12px;color:var(--mu);margin-bottom:12px">
        Isi berat untuk jenis sampah yang disetorkan (kosongkan jika tidak ada). Gunakan titik atau koma untuk desimal (maks. 2 desimal).
    </p>

    <div class="frow" style="flex-wrap:wrap">
        @foreach($jenisList as $index => $jenis)
            <label style="flex:1 1 200px">
                {{ $jenis->nama }} ({{ $jenis->satuan }})
                <br>
                <small style="color:var(--em)">@ Rp {{ number_format($jenis->harga_saat_ini, 0, ',', '.') }} / {{ $jenis->satuan }}</small>
                
                <input type="hidden" name="items[{{ $index }}][jenis_sampah_id]" value="{{ $jenis->id }}">
                <input type="text"
                       name="items[{{ $index }}][berat_kg]"
                       class="input-berat"
                       data-harga="{{ $jenis->harga_saat_ini }}"
                       inputmode="decimal"
                       placeholder="0,00"
                       value="{{ old("items.{$index}.berat_kg") }}">
            </label>
        @endforeach
    </div>
    @error('items')<span class="err" style="display:block">{{ $message }}</span>@enderror

    <div class="box g" style="margin:20px 0;padding:16px;background:rgba(255,255,255,0.03)">
        <p class="lead" id="preview-total" style="font-size:18px;margin:0">
            Total Estimasi Nilai: <b>Rp 0</b> (0,00 kg)
        </p>
    </div>

    <div class="row" style="gap:8px">
        <button class="btn" type="submit">Simpan Setoran</button>
        <a href="{{ route('admin.setoran.index') }}" class="btn o" style="text-decoration:none">Batal</a>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const inputs = document.querySelectorAll('.input-berat');
        const preview = document.getElementById('preview-total');

        function hitungTotal() {
            let totalNilai = 0;
            let totalBerat = 0;

            inputs.forEach(inp => {
                const valStr = (inp.value || '').replace(',', '.').trim();
                const berat = parseFloat(valStr);
                const harga = parseFloat(inp.dataset.harga || 0);

                if (!isNaN(berat) && berat > 0) {
                    totalBerat += berat;
                    totalNilai += Math.round(berat * harga);
                }
            });

            const nilaiFmt = new Intl.NumberFormat('id-ID').format(totalNilai);
            const beratFmt = totalBerat.toFixed(2).replace('.', ',');
            preview.innerHTML = `Total Estimasi Nilai: <b>Rp ${nilaiFmt}</b> (${beratFmt} kg)`;
        }

        inputs.forEach(inp => {
            inp.addEventListener('input', hitungTotal);
        });

        hitungTotal();

        // Ganti koma ke titik saat submit
        document.getElementById('form-setoran').addEventListener('submit', function() {
            inputs.forEach(inp => {
                if (inp.value) {
                    inp.value = inp.value.replace(',', '.').trim();
                }
            });
        });
    });
</script>
@endsection
