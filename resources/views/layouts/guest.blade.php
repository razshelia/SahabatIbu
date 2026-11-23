<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex">
    <title>{{ $title ?? 'Bank Sampah Sahabat Ibu' }}</title>
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
</head>
<body data-view="{{ $dataView ?? 'luar' }}">
    @include('partials.icons')

    <header id="top">
        <a class="brand g" href="{{ url('/') }}">
            <img class="logo" src="{{ asset('assets/logo-mark.svg') }}" alt="" width="34" height="34">
            <div><b>Sahabat Ibu</b><span>Bank Sampah, Jember</span></div>
        </a>
        <a class="g tl" href="{{ url('/') }}">
            <svg class="i"><use href="#i-home"/></svg> Kembali ke garasi
        </a>
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
    <script defer src="{{ asset('assets/js/alat.js') }}"></script>
    <script defer src="{{ asset('assets/js/pintu.js') }}"></script>
    <script defer src="{{ asset('assets/js/aksi.js') }}"></script>
    <script defer src="{{ asset('assets/js/latar.js') }}"></script>
    <script>
        // Auto-hide toast after 5s
        document.addEventListener('DOMContentLoaded', () => {
            const t = document.getElementById('toast');
            if (t && t.classList.contains('on')) {
                setTimeout(() => t.classList.remove('on'), 5000);
            }
        });
    </script>
</body>
</html>
