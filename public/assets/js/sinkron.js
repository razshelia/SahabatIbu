/* Penghubung form panel alat (.alat) <-> layar alat di ruangan 3D.
   - [data-ikut="nama_field"]     : teks di layar LCD halaman mengikuti isian form (radio/teks).
   - [data-ikut-alamat]           : ringkasan alamat (Blok X No. Y, atau alamat bebas untuk nasabah umum).
   - [data-ikut-rp="id"]          : teks "Rp ..." mengikuti isian rupiah (partials/rupiah-input).
   - wrapper rupiah dengan data-draf="capit|rak|timbang" juga mengirim angkanya ke layar alat 3D.
   - tombol .key yang ditekan membuat alat di ruangan "bergerak" sedikit.
   Semua aman dijalankan tanpa 3D (window.ALAT belum ada / WebGL gagal). */
(function () {
    'use strict';

    var rp = function (n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); };
    var alat3d = function () { return window.ALAT || null; };

    // 1. tombol ditekan -> alat 3D bereaksi
    document.addEventListener('click', function (e) {
        var b = e.target.closest ? e.target.closest('.alat .key') : null;
        if (!b || !alat3d()) { return; }
        var a = b.closest('.alat');
        if (a && a.dataset.alat) { alat3d().pulse(a.dataset.alat); }
    });

    // 2. isian rupiah
    document.addEventListener('rupiah', function (e) {
        var w = e.target, n = e.detail || 0, id = w.dataset.nama;
        document.querySelectorAll('[data-ikut-rp="' + id + '"]').forEach(function (el) {
            el.textContent = n > 0 ? rp(n) : (el.dataset.kosong || 'Belum diisi');
            el.classList.toggle('kosong', n <= 0);
        });
        if (w.dataset.draf && alat3d()) { alat3d().set(w.dataset.draf, n); }
    });

    // 3. teks layar yang mengikuti isian form
    function nilai(scope, nm) {
        var els = scope.querySelectorAll('[name="' + nm + '"]');
        if (!els.length) { return ''; }
        if (els[0].type === 'radio') {
            var c = scope.querySelector('[name="' + nm + '"]:checked');
            return c ? c.value : '';
        }
        return (els[0].value || '').trim();
    }

    function peta(el, v) {
        if (!el.dataset.peta) { return v; }
        var out = v;
        el.dataset.peta.split('|').forEach(function (p) {
            var kv = p.split('=');
            if (kv[0] === v) { out = kv[1]; }
        });
        return out;
    }

    function segarkan(scope) {
        scope.querySelectorAll('[data-ikut]').forEach(function (el) {
            var v = peta(el, nilai(scope, el.dataset.ikut));
            el.textContent = v || el.dataset.kosong || '-';
            el.classList.toggle('kosong', !v);
        });
        scope.querySelectorAll('[data-ikut-alamat]').forEach(function (el) {
            var umum = nilai(scope, 'kategori') === 'umum', t;
            if (umum) {
                t = nilai(scope, 'alamat');
            } else {
                var b = nilai(scope, 'blok'), n = nilai(scope, 'nomor_rumah');
                t = (b || n) ? ('Blok ' + (b || '?') + ' No. ' + (n || '?')) : '';
            }
            el.textContent = t || el.dataset.kosong || 'Alamat belum diisi';
            el.classList.toggle('kosong', !t);
        });
    }

    function pasang() {
        // isian rupiah yang sudah berisi (mis. setelah validasi gagal) disampaikan sekali di awal
        document.querySelectorAll('[data-rupiah]').forEach(function (w) {
            var i = w.querySelector('input');
            w.dispatchEvent(new CustomEvent('rupiah', { bubbles: true, detail: parseInt((i && i.value) || '0', 10) || 0 }));
        });
        document.querySelectorAll('.alat').forEach(function (scope) {
            if (!scope.querySelector('[data-ikut],[data-ikut-alamat]')) { return; }
            var f = function () { segarkan(scope); };
            scope.addEventListener('input', f);
            scope.addEventListener('change', f);
            f();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', pasang);
    } else {
        pasang();
    }
})();
