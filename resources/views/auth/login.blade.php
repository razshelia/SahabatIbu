@extends('layouts.guest', ['title' => 'Masuk · Bank Sampah Sahabat Ibu', 'dataView' => 'luar'])

@section('content')
<section class="box g">
    <small class="kick">Masuk</small>
    <h1>Selamat datang, <em>Ibu</em></h1>
    <p class="lead">Masuk dengan email atau akun Google.</p>

    <a href="{{ route('auth.google') }}" class="btn o" style="display:flex;align-items:center;justify-content:center;gap:8px;text-decoration:none">
        <svg class="i" style="width:18px;height:18px"><use href="#i-google"/></svg>
        Lanjut dengan Google
    </a>

    <div class="sep">atau</div>

    <form class="f" method="POST" action="{{ route('login') }}" novalidate>
        @csrf
        <label for="email">
            Email
            <input id="email" name="email" type="email" autocomplete="username" value="{{ old('email') }}" maxlength="255" required autofocus>
            @error('email')
                <span class="err" style="display:block">{{ $message }}</span>
            @enderror
        </label>

        <label for="password" style="position:relative">
            Kata sandi
            <span class="pw-wrap" style="display:block;position:relative">
                <input id="password" name="password" type="password" autocomplete="current-password" required style="padding-right:44px;width:100%;box-sizing:border-box">
                <button type="button" class="pw-eye" onclick="togglePw('password','icon-pw-login')" aria-label="Tampilkan kata sandi" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:var(--mu)">
                    <svg class="i" id="icon-pw-login" style="width:20px;height:20px"><use href="#i-eye"/></svg>
                </button>
            </span>
            @error('password')
                <span class="err" style="display:block">{{ $message }}</span>
            @enderror
        </label>

        <button class="btn" type="submit">Masuk</button>
    </form>

    <script>
    function togglePw(inputId, iconId) {
        var inp = document.getElementById(inputId);
        var ico = document.getElementById(iconId);
        if (!inp || !ico) return;
        if (inp.type === 'password') {
            inp.type = 'text';
            ico.querySelector('use').setAttribute('href', '#i-eye-off');
        } else {
            inp.type = 'password';
            ico.querySelector('use').setAttribute('href', '#i-eye');
        }
    }
    </script>

    <p class="sub">
        <a class="lk" href="{{ route('password.forgot.form') }}">Lupa kata sandi?</a><br>
        Belum punya akun? <a class="lk" href="{{ route('register') }}">Daftar sebagai nasabah</a>
    </p>
</section>
@endsection
