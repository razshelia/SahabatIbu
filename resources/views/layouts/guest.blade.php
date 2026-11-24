<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex">
    <title>{{ $title ?? 'Bank Sampah Sahabat Ibu' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}?v=8">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
</head>
<body data-view="{{ $dataView ?? 'luar' }}">
    @include('partials.icons')

    <header id="top">
        @if (request()->routeIs('profile.complete.form'))
            <div class="brand g">
                <img class="logo" src="{{ asset('assets/logo-mark.svg') }}" alt="" width="34" height="34">
                <div><b>Sahabat Ibu</b><span>Bank Sampah, Jember</span></div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button class="g tl" type="submit" style="border:0;font-family:inherit;font-size:inherit">Kembali dan keluar</button>
            </form>
        @else
            <a class="brand g" href="{{ url('/') }}">
                <img class="logo" src="{{ asset('assets/logo-mark.svg') }}" alt="" width="34" height="34">
                <div><b>Sahabat Ibu</b><span>Bank Sampah, Jember</span></div>
            </a>
            <a class="g tl" href="{{ url('/') }}">
                <svg class="i"><use href="#i-home"/></svg> Kembali ke beranda
            </a>
        @endif
    </header>

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
    @elseif(session('status'))
        <div id="toast" class="on" role="status" aria-live="polite">
            {{ session('status') }}
        </div>
    @else
        <div id="toast" role="status" aria-live="polite"></div>
    @endif

    <main class="pgm">
        @yield('content')
    </main>

    @include('partials.wa-admin')
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script defer src="{{ asset('assets/js/inti.js') }}"></script>
    <script defer src="{{ asset('assets/js/ruangan.js') }}"></script>
    <script defer src="{{ asset('assets/js/alat.js') }}?v=2"></script>
    <script defer src="{{ asset('assets/js/pintu.js') }}"></script>
    <script defer src="{{ asset('assets/js/aksi.js') }}?v=2"></script>
    <script defer src="{{ asset('assets/js/latar.js') }}?v=2"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/lenis@1.1.20/dist/lenis.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/motion@11.18.2/dist/motion.js"></script>
    <script defer src="{{ asset('assets/js/gerak.js') }}"></script>
    <script defer src="{{ asset('assets/js/kilau-kartu.js') }}?v=2"></script>
    <script>
        // Auto-hide toast after 5s
        document.addEventListener('DOMContentLoaded', () => {
            const t = document.getElementById('toast');
            if (t && t.classList.contains('on')) {
                setTimeout(() => t.classList.remove('on'), 5000);
            }
        });
    </script>
    <script defer src="{{ asset('assets/js/validasi-form.js') }}?v=5"></script>
</body>
</html>
