@extends('layouts.guest', ['title' => 'Ganti kata sandi · Bank Sampah Sahabat Ibu', 'dataView' => 'scan'])

@section('content')
<section class="box g">
    <small class="kick">Keamanan akun</small>
    <h1>Ganti <em>kata sandi</em></h1>
    <p class="lead">
        {{ $isMustChange ? 'Demi keamanan, akun wajib mengganti kata sandi sebelum membuka halaman lain.' : 'Perbarui kata sandi akun Ibu secara berkala.' }}
    </p>

    <form class="f" method="POST" action="{{ route('password.change.update') }}" novalidate>
        @csrf

        @if(! $isMustChange)
            <label for="current_password" style="position:relative">
                Kata sandi lama
                <span class="pw-wrap" style="display:block;position:relative">
                    <input id="current_password" name="current_password" type="password" autocomplete="current-password" required style="padding-right:44px;width:100%;box-sizing:border-box">
                    <button type="button" onclick="togglePw('current_password','icon-cp-cur')" aria-label="Tampilkan" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:var(--mu)">
                        <svg class="i" id="icon-cp-cur" style="width:20px;height:20px"><use href="#i-eye"/></svg>
                    </button>
                </span>
                @error('current_password')<span class="err" style="display:block">{{ $message }}</span>@enderror
            </label>
        @endif

        <label for="password" style="position:relative">
            Kata sandi baru (min. 8, huruf dan angka)
            <span class="pw-wrap" style="display:block;position:relative">
                <input id="password" name="password" type="password" autocomplete="new-password" required autofocus style="padding-right:44px;width:100%;box-sizing:border-box">
                <button type="button" onclick="togglePw('password','icon-cp-new')" aria-label="Tampilkan" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:var(--mu)">
                    <svg class="i" id="icon-cp-new" style="width:20px;height:20px"><use href="#i-eye"/></svg>
                </button>
            </span>
            @error('password')<span class="err" style="display:block">{{ $message }}</span>@enderror
        </label>

        <label for="password_confirmation" style="position:relative">
            Ulangi kata sandi baru
            <span class="pw-wrap" style="display:block;position:relative">
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required style="padding-right:44px;width:100%;box-sizing:border-box">
                <button type="button" onclick="togglePw('password_confirmation','icon-cp-conf')" aria-label="Tampilkan" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:var(--mu)">
                    <svg class="i" id="icon-cp-conf" style="width:20px;height:20px"><use href="#i-eye"/></svg>
                </button>
            </span>
        </label>

        <button class="btn" type="submit">Simpan dan lanjut</button>
    </form>
</section>

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
@endsection
