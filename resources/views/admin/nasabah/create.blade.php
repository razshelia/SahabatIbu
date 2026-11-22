@extends('layouts.app', ['title' => 'Tambah Nasabah · Bank Sampah Sahabat Ibu', 'dataView' => 'scan', 'pageTitle' => 'Tambah Nasabah'])

@section('content')
<small class="kick">Pendaftar &amp; nasabah</small>
<h1>Tambah <em>nasabah baru</em></h1>
<p class="lead">Nasabah yang didaftarkan langsung oleh pengurus akan otomatis berstatus <em>Aktif</em>.</p>

<form class="f" method="POST" action="{{ route('admin.nasabah.store') }}" style="max-width:560px" novalidate>
    @csrf

    <label for="name">
        Nama lengkap
        <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" maxlength="100" required>
        @error('name')<span class="err" style="display:block">{{ $message }}</span>@enderror
    </label>

    <label for="email">
        Email
        <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" maxlength="255" required>
        @error('email')<span class="err" style="display:block">{{ $message }}</span>@enderror
    </label>

    <label for="telepon">
        Telepon (angka / tanda +)
        <input id="telepon" name="telepon" type="tel" autocomplete="tel" value="{{ old('telepon') }}" maxlength="20" required>
        @error('telepon')<span class="err" style="display:block">{{ $message }}</span>@enderror
    </label>

    <label for="alamat">
        Alamat
        <input id="alamat" name="alamat" type="text" autocomplete="street-address" value="{{ old('alamat') }}" maxlength="255" required>
        @error('alamat')<span class="err" style="display:block">{{ $message }}</span>@enderror
    </label>

    <label for="password">
        Kata sandi awal (min. 8, kombinasi huruf &amp; angka)
        <input id="password" name="password" type="password" autocomplete="new-password" required>
        @error('password')<span class="err" style="display:block">{{ $message }}</span>@enderror
    </label>

    <div class="row" style="margin-top:16px;gap:8px">
        <button class="btn" type="submit">Tambah Nasabah</button>
        <a href="{{ route('admin.nasabah.index') }}" class="btn o" style="text-decoration:none">Batal</a>
    </div>
</form>
@endsection
