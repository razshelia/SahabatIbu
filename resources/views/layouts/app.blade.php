<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex">
    <title>{{ $title ?? 'Portal Bank Sampah Sahabat Ibu' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}?v=12">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('assets/alat.css') }}?v=4">
    <script>
        window.onAppPageReady = function (callback) {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', callback, { once: true });
                return;
            }
            callback();
        };
    </script>
</head>
<body data-view="{{ $dataView ?? 'home' }}" data-role="{{ auth()->user()->role }}">
    @include('partials.icons')

    <header id="top">
        <div class="crumb g">
            <button class="burger" onclick="toggleSide(true)" aria-label="Buka menu">
                <svg class="i"><use href="#i-menu"/></svg>
            </button>
            <div>
                <b>{{ $pageTitle ?? 'Dashboard' }}</b>
                <small>{{ auth()->user()->role === 'admin' ? 'Pengurus' : 'Nasabah' }} · Sahabat Ibu</small>
            </div>
        </div>
        <div class="right">
            <div class="bal g">
                @if(auth()->user()->role === 'admin')
                    <small>Pengajuan menunggu</small>
                    <b>{{ \App\Models\PenarikanDana::where('status', 'menunggu')->count() }}</b>
                @else
                    <small>Saldo tersedia</small>
                    <b>Rp {{ number_format(auth()->user()->nasabah?->saldo_tersedia ?? 0, 0, ',', '.') }}</b>
                @endif
            </div>
        </div>
    </header>

    <aside id="side" class="g" aria-label="Menu utama">
        <a class="brand" href="{{ url('/') }}" style="text-decoration:none">
            <img class="logo" src="{{ asset('assets/logo-mark.svg') }}" alt="" width="34" height="34">
            <div><b>Sahabat Ibu</b><span>Bank Sampah, Jember</span></div>
        </a>
        <nav id="keys">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'on' : '' }}">
                    <svg class="i"><use href="#i-home"/></svg><span class="t">Dashboard</span>
                </a>
                <a href="{{ route('admin.nasabah.index') }}" class="{{ request()->routeIs('admin.nasabah.index') || request()->routeIs('admin.nasabah.edit') || request()->routeIs('admin.nasabah.create') ? 'on' : '' }}">
                    <svg class="i"><use href="#i-users"/></svg><span class="t">Pendaftar &amp; nasabah</span>
                </a>
                <a href="{{ route('admin.katalog.index') }}" class="{{ request()->routeIs('admin.katalog.*') ? 'on' : '' }}">
                    <svg class="i"><use href="#i-tag"/></svg><span class="t">Katalog harga</span>
                </a>
                <a href="{{ route('admin.setoran.index') }}" class="{{ request()->routeIs('admin.setoran.*') ? 'on' : '' }}">
                    <svg class="i"><use href="#i-scale"/></svg><span class="t">Setoran</span>
                </a>
                <a href="{{ route('admin.penarikan.index') }}" class="{{ request()->routeIs('admin.penarikan.*') ? 'on' : '' }}">
                    <svg class="i"><use href="#i-cash"/></svg><span class="t">Penarikan dana</span>
                </a>
                <a href="{{ route('admin.laporan.index') }}" class="{{ request()->routeIs('admin.laporan.*') ? 'on' : '' }}">
                    <svg class="i"><use href="#i-receipt"/></svg><span class="t">Laporan</span>
                </a>
                <a href="{{ route('admin.profile.show') }}" class="{{ request()->routeIs('admin.profile.*') ? 'on' : '' }}">
                    <svg class="i"><use href="#i-user"/></svg><span class="t">Profil &amp; aktivitas</span>
                </a>
            @else
                <a href="{{ route('nasabah.dashboard') }}" class="{{ request()->routeIs('nasabah.dashboard') ? 'on' : '' }}">
                    <svg class="i"><use href="#i-home"/></svg><span class="t">Dashboard</span>
                </a>
                <a href="{{ route('nasabah.setoran.index') }}" class="{{ request()->routeIs('nasabah.setoran.*') ? 'on' : '' }}">
                    <svg class="i"><use href="#i-scale"/></svg><span class="t">Riwayat setoran</span>
                </a>
                <a href="{{ route('nasabah.penarikan.index') }}" class="{{ request()->routeIs('nasabah.penarikan.*') ? 'on' : '' }}">
                    <svg class="i"><use href="#i-cash"/></svg><span class="t">Penarikan dana</span>
                </a>
                <a href="{{ route('nasabah.katalog.index') }}" class="{{ request()->routeIs('nasabah.katalog.*') ? 'on' : '' }}">
                    <svg class="i"><use href="#i-tag"/></svg><span class="t">Katalog harga</span>
                </a>
                <a href="{{ route('nasabah.profile.show') }}" class="{{ request()->routeIs('nasabah.profile.*') ? 'on' : '' }}">
                    <svg class="i"><use href="#i-user"/></svg><span class="t">Profil</span>
                </a>
            @endif

            <small class="gl">Lainnya</small>
            <a href="{{ route('garasi') }}" class="{{ request()->routeIs('garasi') ? 'on' : '' }}">
                <svg class="i"><use href="#i-door"/></svg><span class="t">Coba garasi 3D</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" id="form-logout" style="margin:0">
                @csrf
                <button type="submit" class="keluar" style="width:100%;background:transparent;border:0;text-align:left;padding:0">
                    <svg class="i"><use href="#i-key"/></svg><span class="t">Keluar</span>
                </button>
            </form>
        </nav>
        <button class="fold" id="fold" onclick="toggleSide()" aria-label="Ciutkan atau perluas menu">
            <svg class="i"><use href="#i-chev"/></svg><span class="t">Ciutkan menu</span>
        </button>
    </aside>
    <div id="scrim" onclick="toggleSide(false)"></div>

    @if(session('success'))
        <div id="toast" class="on" role="status" aria-live="polite">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div id="toast" class="on" style="background:#4a1b1b;border-color:#ff6b6b;" role="status" aria-live="polite">
            {{ session('error') }}
        </div>
    @elseif(session('warning'))
        <div id="toast" class="on" style="background:#4a391b;border-color:#ffcc66;" role="status" aria-live="polite">
            {{ session('warning') }}
        </div>
    @elseif(session('info'))
        <div id="toast" class="on" style="background:#1d2e3a;border-color:#6ba4ff;" role="status" aria-live="polite">
            {{ session('info') }}
        </div>
    @else
        <div id="toast" role="status" aria-live="polite"></div>
    @endif

    <main class="pgm top">
        <section class="box isi-lebar g" id="isi">
            @yield('content')
        </section>
    </main>

    @php
        $garasiData = \App\Support\GarasiData::safe(auth()->user());
    @endphp
    <script id="garasi-data" type="application/json">{!! json_encode($garasiData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script>window.GARASI = JSON.parse(document.getElementById('garasi-data').textContent);</script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script defer src="{{ asset('assets/js/inti.js') }}"></script>
    <script defer src="{{ asset('assets/js/dekor.js') }}"></script>
    <script defer src="{{ asset('assets/js/ruangan.js') }}"></script>
    <script defer src="{{ asset('assets/js/alat.js') }}?v=2"></script>
    <script defer src="{{ asset('assets/js/pintu.js') }}"></script>
    <script defer src="{{ asset('assets/js/aksi.js') }}?v=2"></script>
    <script defer src="{{ asset('assets/js/latar.js') }}?v=4"></script>
    <script defer src="{{ asset('assets/js/sinkron.js') }}?v=2"></script>
    <button type="button" id="lihat" onclick="toggleLihat()" aria-pressed="false">Lihat ruangan penuh</button>
    <button type="button" id="pusat" aria-label="Kembalikan sudut pandang ruangan">Pusatkan ruangan</button>
    @include('partials.wa-admin')
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/lenis@1.1.20/dist/lenis.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/motion@11.18.2/dist/motion.js"></script>
    <script defer src="{{ asset('assets/js/gerak.js') }}"></script>
    <script defer src="{{ asset('assets/js/kilau-kartu.js') }}?v=2"></script>
    <script defer src="{{ asset('assets/js/menu.js') }}"></script>
    <script defer src="{{ asset('assets/js/validasi-form.js') }}?v=6"></script>
    <script>
        function toggleLihat() {
            const on = document.documentElement.classList.toggle('lihat');
            const b = document.getElementById('lihat');
            b.textContent = on ? 'Kembali ke halaman' : 'Lihat ruangan penuh';
            b.setAttribute('aria-pressed', on);
            window.dispatchEvent(new Event('resize'));
        }
        function toggleSide(open) {
            const side = document.getElementById('side');
            const scrim = document.getElementById('scrim');
            if (typeof open === 'boolean') {
                side.classList.toggle('open', open);
                scrim.classList.toggle('on', open);
            } else {
                side.classList.toggle('open');
                scrim.classList.toggle('on');
            }
        }

        window.onAppPageReady(() => {
            const t = document.getElementById('toast');
            if (t && t.classList.contains('on')) {
                setTimeout(() => t.classList.remove('on'), 5000);
            }
        });
    </script>
    <div id="page-scripts" hidden>@stack('scripts')</div>
    <script defer src="{{ asset('assets/js/navigasi-aplikasi.js') }}?v=1"></script>
</body>
</html>