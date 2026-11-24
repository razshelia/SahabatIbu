/* Gerak halus: Lenis (smooth scroll) + ScrollTrigger (pemicu saat digulir) + Motion (animasi masuk & hover).
   Jika pustaka gagal dimuat, atau pengguna meminta gerak dikurangi, berkas ini tidak berbuat apa-apa
   dan tampilan lama (CSS) tetap berjalan. */
(function () {
    'use strict';
    if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    if (!(window.Motion && window.gsap && window.ScrollTrigger && window.Lenis)) return;

    var animate = Motion.animate, stagger = Motion.stagger;
    gsap.registerPlugin(ScrollTrigger);
    var EASE = [0.22, 1, 0.36, 1];
    var root = document.documentElement;
    root.classList.add('mo');

    /* ---------- Lenis: smooth scroll, sinkron dengan ScrollTrigger ---------- */
    var lenis = new Lenis({
        duration: 1.15,
        easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); },
        smoothWheel: true,
        // Area yang punya gulungan sendiri dibiarkan natural.
        prevent: function (n) {
            if (n.closest('dialog,#side,#keys,.tbl,textarea,select,[data-lenis-prevent]')) return true;
            var c = n.closest('.card'); // kartu yang isinya melebihi layar menggulir sendiri
            return !!(c && c.scrollHeight > c.clientHeight + 2);
        }
    });
    window.lenis = lenis;
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add(function (t) { lenis.raf(t * 1000); });
    gsap.ticker.lagSmoothing(0);

    /* ---------- Pengaman: jika animasi gagal di peramban ini, teks harus tetap tampil ---------- */
    function aman(fn) {
        try { return fn(); } catch (e) {
            root.classList.remove('mo'); // kembali ke tampilan CSS biasa: semua teks terlihat
            if (window.console) console.warn('gerak.js dimatikan:', e);
        }
    }
    window.addEventListener('error', function (e) {
        if (e && e.filename && /gerak\.js/.test(e.filename)) root.classList.remove('mo');
    });

    /* ---------- Bantu ---------- */
    function isiKartu(card) {
        var out = [];
        Array.prototype.forEach.call(card.children, function (k) {
            if (k.matches('script,style,.skel,[hidden]')) return;
            if (k.matches('.grid,.chips,.how,.row')) out.push.apply(out, k.children);
            else out.push(k);
        });
        return out;
    }

    /* Kartu sendiri TIDAK pernah diberi opacity < 1: opacity pada induk mematikan backdrop-filter
       milik lapisan kaca (::after) sehingga kaca "muncul tiba-tiba" di akhir fade (terlihat berkedip).
       Yang di-fade adalah isi kartu dan lapisan kaca lewat variabel --sc. */
    function masuk(card) {
        clearTimeout(card._tk); // batal menyembunyikan bila batas terlewati bolak-balik
        if (card.dataset.mo === '1') return; // sudah tampil: jangan diulang (mencegah kedip)
        card.dataset.mo = '1';
        var kids = isiKartu(card);
        aman(function () { animate(card, { y: [36, 0], scale: [0.97, 1] }, { duration: 0.8, ease: EASE }); });
        aman(function () { animate(card, { '--sc': [0, 1] }, { duration: 0.8, ease: EASE }); });
        aman(function () {
            if (kids.length) animate(kids, { opacity: [0, 1], y: [18, 0] },
                { duration: 0.6, ease: EASE, delay: stagger(0.07, { startDelay: 0.12 }) });
        });
        // Jaring pengaman: bila 1,6 dtk kemudian isi kartu masih transparan padahal kartu tampil, paksa terlihat.
        clearTimeout(card._ck);
        card._ck = setTimeout(function () {
            if (card.dataset.mo !== '1') return;
            kids.forEach(function (k) { if (+getComputedStyle(k).opacity < 0.05) k.style.opacity = '1'; });
            if (+getComputedStyle(card).getPropertyValue('--sc') < 0.05) card.style.setProperty('--sc', '1');
        }, 1600);
    }

    function keluarTunda(card) {
        clearTimeout(card._tk);
        card._tk = setTimeout(function () { keluar(card); }, 260);
    }

    function keluar(card) {
        if (card.dataset.mo !== '1') return;
        card.dataset.mo = '0';
        clearTimeout(card._ck);
        var kids = isiKartu(card);
        aman(function () { animate(card, { y: 24, scale: 0.98 }, { duration: 0.35, ease: 'easeIn' }); });
        aman(function () { animate(card, { '--sc': 0 }, { duration: 0.35, ease: 'easeIn' }); });
        aman(function () { if (kids.length) animate(kids, { opacity: 0 }, { duration: 0.3, ease: 'easeIn' }); });
    }

    /* ---------- Hover (kaca): angkat sedikit + kilau mengikuti kursor ---------- */
    function pasangHover(el) {
        if (el.dataset.moHover) return;
        el.dataset.moHover = '1';
        function masukHover(e) {
            if (e.pointerType === 'touch' || el.dataset.mo === '0' || el.classList.contains('glow')) return;
            if (lenis.isScrolling) return; // kartu bergerak di bawah kursor saat digulir, bukan hover sungguhan
            el.classList.add('glow');
            animate(el, { y: -6, scale: 1.015 }, { duration: 0.3, ease: EASE });
        }
        el.addEventListener('pointerenter', masukHover);
        el.addEventListener('pointermove', function (e) {
            masukHover(e);
            var r = el.getBoundingClientRect();
            el.style.setProperty('--mx', (e.clientX - r.left) + 'px');
            el.style.setProperty('--my', (e.clientY - r.top) + 'px');
        });
        el.addEventListener('pointerleave', function () {
            el.classList.remove('glow');
            if (el.dataset.mo === '0') return;
            animate(el, { y: 0, scale: 1 }, { duration: 0.35, ease: EASE });
        });
        el.addEventListener('pointerdown', function () {
            if (el.dataset.mo !== '0') animate(el, { scale: 0.98 }, { duration: 0.12 });
        });
        el.addEventListener('pointerup', function () {
            if (el.dataset.mo !== '0') animate(el, { scale: el.matches(':hover') ? 1.015 : 1 }, { duration: 0.2, ease: EASE });
        });
    }

    /* ---------- Halaman garasi: kartu tiap bagian muncul saat bagiannya di tengah layar ---------- */
    var secs = document.querySelectorAll('.sec');
    if (secs.length) {
        secs.forEach(function (sec) {
            // .wide yang membungkus .card (bagian penutup) hanya wadah; yang dianimasikan kartunya.
            var cards = Array.prototype.filter.call(sec.querySelectorAll('.card, .wide'), function (c) {
                if (c.classList.contains('wide') && c.querySelector('.card')) return false;
                if (c.classList.contains('wide')) c.classList.add('mo-w');
                return true;
            });
            cards.forEach(function (c) { if (c.classList.contains('g')) pasangHover(c); });
            ScrollTrigger.create({
                trigger: sec, start: 'top 60%', end: 'bottom 40%',
                onEnter: function () { cards.forEach(masuk); },
                onEnterBack: function () { cards.forEach(masuk); },
                onLeave: function () { cards.forEach(keluarTunda); },
                onLeaveBack: function () { cards.forEach(keluarTunda); }
            });
        });
        document.querySelectorAll('.grid > div, .how > div').forEach(pasangHover);
    }

    /* ---------- Halaman lain (dashboard, login, dll.): kotak muncul, isinya menyusul saat terlihat ---------- */
    document.querySelectorAll('main.pgm .box').forEach(function (box) {
        animate(box, { opacity: [0, 1], y: [28, 0], scale: [0.98, 1] }, { duration: 0.7, ease: EASE });
        isiKartu(box).forEach(function (k, i) {
            var r = k.getBoundingClientRect();
            if (r.top < innerHeight * 0.92) {
                animate(k, { opacity: [0, 1], y: [16, 0] }, { duration: 0.6, ease: EASE, delay: 0.1 + Math.min(i, 8) * 0.06 });
                return;
            }
            k.style.opacity = '0';
            ScrollTrigger.create({
                trigger: k, start: 'top 92%', once: true,
                onEnter: function () { animate(k, { opacity: [0, 1], y: [18, 0] }, { duration: 0.6, ease: EASE }); }
            });
        });
    });

    // Tinggi halaman berubah setelah konten/gambar/font selesai dimuat: hitung ulang posisi pemicu.
    var tunda = 0;
    function hitungUlang() { clearTimeout(tunda); tunda = setTimeout(function () { ScrollTrigger.refresh(); }, 120); }
    window.addEventListener('load', hitungUlang);
    if (window.ResizeObserver) new ResizeObserver(hitungUlang).observe(document.body);
})();
