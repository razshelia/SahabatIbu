@extends('layouts.guest', ['title' => 'Daftar · Bank Sampah Sahabat Ibu', 'dataView' => 'luar'])

@section('content')
<section class="box g">
    <small class="kick">Pendaftaran nasabah</small>
    <h1>Daftar jadi <em>nasabah</em></h1>
    <p class="lead">Setelah mendaftar, pengurus akan memeriksa data Ibu sebelum akun bisa digunakan.</p>

    <a href="{{ route('auth.google') }}" class="btn o" style="display:flex;align-items:center;justify-content:center;gap:8px;text-decoration:none">
        <svg class="i" style="width:18px;height:18px"><use href="#i-google"/></svg>
        Lanjut dengan Google
    </a>

    <div class="sep">atau</div>

    <form class="f" method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        <label for="name">
            Nama lengkap
            <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" maxlength="100" required>
            @error('name')<span class="err" data-validation-error-for="name" style="display:block">{{ $message }}</span>@enderror
        </label>

        <label for="email">
            Email
            <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" maxlength="255" required>
            @error('email')<span class="err" data-validation-error-for="email" style="display:block">{{ $message }}</span>@enderror
        </label>

        <label for="telepon">
            Telepon
            <input id="telepon" name="telepon" type="tel" autocomplete="tel" value="{{ old('telepon') }}" maxlength="20" pattern="[0-9+]+" data-validation-pattern-message="Gunakan angka dan tanda + saja. Hapus spasi atau tanda hubung." required>
            @error('telepon')<span class="err" data-validation-error-for="telepon" style="display:block">{{ $message }}</span>@enderror
        </label>

        @include('partials.alamat-fields')

        <label for="password" style="position:relative">
            Kata sandi (minimal 8 karakter, berisi huruf dan angka)
            <span class="pw-wrap" style="display:block;position:relative">
                <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" pattern="(?=.*[A-Za-z])(?=.*[0-9]).+" data-validation-pattern-message="Tambahkan minimal satu huruf dan satu angka." required style="padding-right:44px;width:100%;box-sizing:border-box">
                <button type="button" class="pw-eye" onclick="togglePw('password','icon-pw-reg')" aria-label="Tampilkan kata sandi" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:var(--mu)">
                    <svg class="i" id="icon-pw-reg" style="width:20px;height:20px"><use href="#i-eye"/></svg>
                </button>
            </span>
            @error('password')<span class="err" data-validation-error-for="password" style="display:block">{{ $message }}</span>@enderror
        </label>

        <label for="password_confirmation" style="position:relative">
            Ulangi kata sandi
            <span class="pw-wrap" style="display:block;position:relative">
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" data-match="password" required style="padding-right:44px;width:100%;box-sizing:border-box">
                <button type="button" class="pw-eye" onclick="togglePw('password_confirmation','icon-pw-conf')" aria-label="Tampilkan ulangi kata sandi" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:var(--mu)">
                    <svg class="i" id="icon-pw-conf" style="width:20px;height:20px"><use href="#i-eye"/></svg>
                </button>
            </span>
        </label>

        <label class="ck" for="persetujuan_privasi">
            <input id="persetujuan_privasi" name="persetujuan_privasi" type="checkbox" value="1" {{ old('persetujuan_privasi') ? 'checked' : '' }} required>
            <span>Saya menyetujui pemrosesan data pribadi saya sesuai <a class="lk" href="{{ route('privacy.policy') }}" target="_blank" rel="noopener">Kebijakan Privasi</a> (UU No. 27 Tahun 2022).</span>
        </label>
        @error('persetujuan_privasi')<span class="err" data-validation-error-for="persetujuan_privasi" style="display:block">{{ $message }}</span>@enderror

        <button class="btn" type="submit">Daftar</button>
    </form>

    <p class="sub">Sudah terdaftar? <a class="lk" href="{{ route('login') }}">Masuk</a></p>
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
