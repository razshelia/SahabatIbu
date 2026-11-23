{{-- Isian alamat nasabah (BR-19). Variabel opsional: $nasabah (untuk nilai awal pada form ubah). --}}
@php
    $nsb = $nasabah ?? null;
    $kat = old('kategori', $nsb->kategori ?? 'warga');
    $blokV = old('blok', $nsb->blok ?? '');
    $noV = old('nomor_rumah', $nsb->nomor_rumah ?? '');
    $alamatV = old('alamat', ($nsb && $nsb->kategori === 'umum') ? $nsb->alamat : '');
@endphp
<fieldset class="alamat-grup" style="border:0;padding:0;margin:0 0 14px">
    <legend style="padding:0;margin-bottom:6px;font-weight:700">Status domisili</legend>
    <label style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
        <input type="radio" name="kategori" value="warga" style="width:auto;margin:0" {{ $kat === 'warga' ? 'checked' : '' }}>
        <span>Warga Perumahan Taman Gading</span>
    </label>
    <label style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
        <input type="radio" name="kategori" value="umum" style="width:auto;margin:0" {{ $kat === 'umum' ? 'checked' : '' }}>
        <span>Umum (bukan warga Taman Gading)</span>
    </label>
    @error('kategori')<span class="err" style="display:block">{{ $message }}</span>@enderror

    <div data-grup="warga" style="margin-top:8px">
        <div style="display:flex;gap:12px">
            <label for="blok" style="flex:1">
                Blok
                <input id="blok" name="blok" type="text" value="{{ $blokV }}" maxlength="10" placeholder="Contoh: B" autocomplete="off">
                @error('blok')<span class="err" style="display:block">{{ $message }}</span>@enderror
            </label>
            <label for="nomor_rumah" style="flex:1">
                Nomor rumah
                <input id="nomor_rumah" name="nomor_rumah" type="text" value="{{ $noV }}" maxlength="10" placeholder="Contoh: 12" autocomplete="off">
                @error('nomor_rumah')<span class="err" style="display:block">{{ $message }}</span>@enderror
            </label>
        </div>
        <small style="color:var(--mu);display:block;margin-top:2px">Pengurus akan mencocokkan blok dan nomor rumah saat verifikasi.</small>
    </div>

    <div data-grup="umum" style="margin-top:8px">
        <label for="alamat">
            Alamat lengkap
            <input id="alamat" name="alamat" type="text" autocomplete="street-address" value="{{ $alamatV }}" maxlength="255">
            @error('alamat')<span class="err" style="display:block">{{ $message }}</span>@enderror
        </label>
        <small style="color:var(--mu);display:block;margin-top:2px">Pendaftar umum disetujui pengurus satu per satu.</small>
    </div>
</fieldset>
<script>
(function () {
    var f = document.currentScript.closest('form');
    if (!f) return;
    function pasang() {
        var k = (f.querySelector('input[name="kategori"]:checked') || {}).value || 'warga';
        f.querySelectorAll('[data-grup]').forEach(function (g) {
            var on = g.getAttribute('data-grup') === k;
            g.hidden = !on;
            g.querySelectorAll('input').forEach(function (i) { i.disabled = !on; });
        });
    }
    f.querySelectorAll('input[name="kategori"]').forEach(function (r) { r.addEventListener('change', pasang); });
    pasang();
})();
</script>
