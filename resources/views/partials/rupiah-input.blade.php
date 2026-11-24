{{--
    Isian nominal rupiah dengan pintasan (hanya angka, tidak bisa minus).
    Tampil sebagai layar angka + papan tombol (gaya panel alat, lihat public/assets/alat.css).
    Variabel: $id, $name, $label, $step (kelipatan tombol +/−), $max (opsional), $value (opsional),
              $placeholder (opsional), $maxLabel (opsional, teks tombol "isi maksimum"),
              $draf (opsional: kunci alat 3D yang ikut menampilkan angka ini, mis. 'capit').
    Setiap perubahan mengirim event 'rupiah' (detail = angka) yang ditangkap public/assets/js/sinkron.js.
--}}
@php
    $rpValue = old($name, $value ?? '');
    $rpStep = (int) ($step ?? 1000);
@endphp
<div class="rp-field">
    <label for="{{ $id }}">{{ $label }}</label>
    <div data-rupiah data-nama="{{ $id }}" data-step="{{ $rpStep }}" @if(!empty($max)) data-max="{{ (int) $max }}" @endif @if(!empty($draf)) data-draf="{{ $draf }}" @endif style="display:grid;gap:12px">
        <div class="rp-lcd">
            <input id="{{ $id }}" class="rp-input" name="{{ $name }}" type="text" inputmode="numeric" pattern="[0-9]*" data-min-value="1" autocomplete="off" maxlength="12" required
                   placeholder="{{ $placeholder ?? '0' }}" value="{{ $rpValue }}">
        </div>
        <div class="rp-papan">
            <button type="button" class="key p" data-aksi="kurang" aria-label="Kurangi {{ number_format($rpStep, 0, ',', '.') }}">−</button>
            <button type="button" class="key p" data-aksi="tambah" aria-label="Tambah {{ number_format($rpStep, 0, ',', '.') }}">+</button>
            <button type="button" class="key y" data-aksi="nol2" aria-label="Tambah dua angka nol">00</button>
            <button type="button" class="key y" data-aksi="nol3" aria-label="Tambah tiga angka nol">000</button>
            @if(!empty($max))
                <button type="button" class="key g" data-aksi="max">{{ $maxLabel ?? 'Maksimum' }}</button>
            @endif
            <button type="button" class="key rp-hapus" data-aksi="hapus">Hapus</button>
        </div>
        <output class="sr" data-tampil aria-live="polite"></output>
        <small>Gunakan tombol + atau − untuk mengubah jumlah sebesar Rp {{ number_format($rpStep, 0, ',', '.') }}. Jumlah tidak bisa kurang dari 0.</small>
    </div>
</div>
@error($name)
    <span class="err" data-validation-error-for="{{ $name }}" style="display:block">{{ $message }}</span>
@enderror
@once
    @push('scripts')
    <script>
    (function () {
        function fmt(n) { return 'Rp ' + n.toLocaleString('id-ID'); }
        document.querySelectorAll('[data-rupiah]').forEach(function (w) {
            var inp = w.querySelector('input'), tampil = w.querySelector('[data-tampil]');
            var step = parseInt(w.dataset.step, 10) || 1000;
            var max = w.dataset.max ? parseInt(w.dataset.max, 10) : null;
            function nilai() { return parseInt(inp.value || '0', 10) || 0; }
            function tulis(n) {
                n = Math.max(0, Math.floor(n));
                if (max !== null && n > max) n = max;
                inp.value = n > 0 ? String(n) : '';
                tampil.textContent = n > 0 ? fmt(n) : '';
                w.dispatchEvent(new CustomEvent('rupiah', { bubbles: true, detail: n }));
            }
            inp.addEventListener('input', function () {
                var d = inp.value.replace(/\D+/g, '').replace(/^0+/, '');
                tulis(d === '' ? 0 : parseInt(d, 10));
            });
            // Cegah tanda minus, plus, desimal, dan huruf e saat mengetik.
            inp.addEventListener('keydown', function (e) {
                if (e.key.length === 1 && !/[0-9]/.test(e.key) && !e.ctrlKey && !e.metaKey) e.preventDefault();
            });
            w.addEventListener('click', function (e) {
                var b = e.target.closest('[data-aksi]');
                if (!b) return;
                var v = nilai(), a = b.dataset.aksi;
                if (a === 'tambah') tulis((Math.floor(v / step) + 1) * step);
                else if (a === 'kurang') tulis(Math.max(0, (Math.ceil(v / step) - 1) * step));
                else if (a === 'nol2') { if (v > 0) tulis(v * 100); }
                else if (a === 'nol3') { if (v > 0) tulis(v * 1000); }
                else if (a === 'max' && max !== null) tulis(max);
                else if (a === 'hapus') tulis(0);
                inp.focus();
            });
            tampil.textContent = nilai() > 0 ? fmt(nilai()) : '';
        });
    })();
    </script>
    @endpush
@endonce
