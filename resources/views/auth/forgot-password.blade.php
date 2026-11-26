@extends('layouts.guest', ['title' => 'Lupa kata sandi · Bank Sampah Sahabat Ibu', 'dataView' => 'luar'])

@section('content')
<section class="box g">
    <small class="kick">Lupa kata sandi</small>
    <h1>Atur ulang <em>kata sandi</em></h1>
    <p class="lead">Masukkan email yang Ibu gunakan untuk masuk. Kami akan mengirim tautan untuk membuat kata sandi baru.</p>

    <form class="f" method="POST" action="{{ route('password.forgot.send') }}" novalidate>
        @csrf
        <label for="email">
            Email
            <input id="email" name="email" type="email" autocomplete="username" value="{{ old('email') }}" maxlength="255" required autofocus>
            @error('email')<span class="err" data-validation-error-for="email" style="display:block">{{ $message }}</span>@enderror
        </label>

        <button class="btn" type="submit">Kirim tautan reset</button>
    </form>

    <p class="sub"><a class="lk" href="{{ route('login') }}">Kembali ke halaman masuk</a></p>
</section>
@endsection
