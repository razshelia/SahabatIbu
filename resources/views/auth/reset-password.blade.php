@extends('layouts.guest', ['title' => 'Atur Ulang Kata Sandi · Bank Sampah Sahabat Ibu', 'dataView' => 'scan'])

@section('content')
<section class="box g">
    <small class="kick">Pemulihan Akun</small>
    <h1>Atur ulang <em>kata sandi</em></h1>
    <p class="lead">Buat kata sandi baru untuk akun Ibu (min. 8 karakter, huruf dan angka).</p>

    <form class="f" method="POST" action="{{ route('password.reset.update') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <label for="email">
            Email
            <input id="email" name="email" type="email" autocomplete="username" value="{{ old('email', $email) }}" required readonly>
            @error('email')<span class="err" style="display:block">{{ $message }}</span>@enderror
        </label>

        <label for="password">
            Kata sandi baru
            <input id="password" name="password" type="password" autocomplete="new-password" required autofocus>
            @error('password')<span class="err" style="display:block">{{ $message }}</span>@enderror
        </label>

        <label for="password_confirmation">
            Ulangi kata sandi baru
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        </label>

        <button class="btn" type="submit">Simpan Kata Sandi</button>
    </form>
</section>
@endsection
