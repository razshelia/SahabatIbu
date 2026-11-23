{{--
    Isian nominal rupiah dengan pintasan (hanya angka, tidak bisa minus).
    Variabel: $id, $name, $label, $step (kelipatan tombol +/−), $max (opsional), $value (opsional),
              $placeholder (opsional), $maxLabel (opsional, teks tombol "isi maksimum").
--}}
@php
    $rpValue = old($name, $value ?? '');
    $rpStep = (int) ($step ?? 1000);
@endphp
@once
    <style>
        .rp-b{min-height:48px;min-width:56px;padding:0 14px;border-radius:14px;border:1px solid var(--line);background:#ffffff14;color:var(--ink);font:inherit;font-weight:800;font-size:16px;cursor:pointer}
        .rp-b:active{transform:translateY(1px)}
        .rp-b.rp-hapus{margin-left:auto}
        .rp-bar{display:flex;flex-wrap:wrap;gap:8px}
        .rp-tampil{font-weight:800;font-size:15px;color:var(--green,#6fe0a2)}
    </style>
@endonce
<div class="rp-field" style="display:grid;gap:6px;min-width:240px">
    <label for="{{ $id }}" style="font-weight:700;font-size:14.5px">{{ $label }}</label>
    <div data-rupiah data-step="{{ $rpStep }}" @if(!empty($max)) data-max="{{ (int) $max }}" @endif style="display:grid;gap:8px">
        <input id="{{ $id }}" name="{{ $name }}" type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="off" maxlength="12" required
               placeholder="{{ $placeholder ?? 'Contoh: 50000' }}" value="{{ $rpValue }}" style="font-size:20px;font-weight:800">
        <div class="rp-bar">
            <button type="button" class="rp-b" data-aksi="kurang" aria-label="Kurangi {{ number_format($rpStep, 0, ',', '.') }}">−</button>
            <button type="button" class="rp-b" data-aksi="tambah" aria-label="Tambah {{ number_format($rpStep, 0, ',', '.') }}">+</button>
            <button type="button" class="rp-b" data-aksi="nol2" aria-label="Tambah dua angka nol">00</button>
            <button type="button" class="rp-b" data-aksi="nol3" aria-label="Tambah tiga angka nol">000</button>
            @if(!empty($max))
                <button type="button" class="rp-b" data-aksi="max">{{ $maxLabel ?? 'Maksimum' }}</button>
            @endif
            <button type="button" class="rp-b rp-hapus" data-aksi="hapus">Hapus</button>
        </div>
        <span class="rp-tampil" data-tampil aria-live="polite"></span>
        <small style="color:var(--mu)">Tombol + dan − bergeser Rp {{ number_format($rpStep, 0, ',', '.') }}. Nilai tidak bisa kurang dari 0.</small>
    </div>
</div>
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
            tulis(nilai());
        });
    })();
    </script>
    @endpush
@endonce
