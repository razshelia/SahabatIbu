@extends('layouts.app', ['title' => 'Profil Nasabah · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Profil Nasabah'])

@section('content')
<small class="kick">Profil</small>
<h1>Data <em>Ibu</em></h1>
<p class="lead">
    <b>{{ $user->name }}</b> · {{ $nasabah->nomor_nasabah }} · {{ $user->email }}
    <span class="pill ok" style="margin-left:8px">{{ ucfirst($user->status) }}</span>
</p>

<h2>Perbarui informasi kontak</h2>
<form class="f" method="POST" action="{{ route('nasabah.profile.update') }}" style="max-width:540px" novalidate>
    @csrf
    @method('PUT')

    <label for="name">
        Nama
        <input id="name" type="text" value="{{ $user->name }}" readonly disabled style="opacity:0.6;cursor:not-allowed">
        <small style="color:var(--mu);margin-top:2px;display:block">Perubahan nama silakan hubungi pengurus.</small>
    </label>

    <label for="email">
        Email
        <input id="email" type="email" value="{{ $user->email }}" readonly disabled style="opacity:0.6;cursor:not-allowed">
        <small style="color:var(--mu);margin-top:2px;display:block">Email akun terikat dan dilindungi.</small>
    </label>

    <label for="telepon">
        Telepon / WhatsApp
        <input id="telepon" name="telepon" type="tel" autocomplete="tel" value="{{ old('telepon', $nasabah->telepon) }}" maxlength="20" required autofocus>
        @error('telepon')<span class="err" style="display:block">{{ $message }}</span>@enderror
    </label>

    <label for="alamat">
        Alamat Domisili
        <input id="alamat" name="alamat" type="text" autocomplete="street-address" value="{{ old('alamat', $nasabah->alamat) }}" maxlength="255" required>
        @error('alamat')<span class="err" style="display:block">{{ $message }}</span>@enderror
    </label>

    <button class="btn" type="submit">Simpan Kontak</button>
</form>

<h2>Ganti kata sandi</h2>
<form class="f" method="POST" action="{{ route('password.change.update') }}" style="max-width:540px;margin-top:16px" novalidate>
    @csrf

    <label for="current_password">
        Kata sandi saat ini
        <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
        @error('current_password')<span class="err" style="display:block">{{ $message }}</span>@enderror
    </label>

    <label for="password">
        Kata sandi baru (min. 8 karakter, kombinasi huruf &amp; angka)
        <input id="password" name="password" type="password" autocomplete="new-password" required>
        @error('password')<span class="err" style="display:block">{{ $message }}</span>@enderror
    </label>

    <label for="password_confirmation">
        Ulangi kata sandi baru
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
    </label>

    <button class="btn" type="submit">Simpan Kata Sandi</button>
</form>

<p class="sub" style="margin-top:24px">
    Pelindungan data Ibu dijamin UU No. 27 Tahun 2022. Baca <a class="lk" href="{{ route('privacy.policy') }}">Kebijakan Privasi</a>.
</p>
@endsection
