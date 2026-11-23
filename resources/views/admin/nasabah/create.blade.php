@extends('layouts.app', ['title' => 'Tambah Nasabah · Bank Sampah Sahabat Ibu', 'dataView' => 'scan', 'pageTitle' => 'Tambah Nasabah'])

@section('content')
<small class="kick">Pendaftar &amp; nasabah</small>
<h1>Tambah <em>nasabah baru</em></h1>
<p class="lead">Nasabah yang didaftarkan langsung oleh pengurus akan otomatis berstatus <em>Aktif</em>.</p>

<form method="POST" action="{{ route('admin.nasabah.store') }}" novalidate>
    @csrf

    <x-alat tool="scan" plat="Kartu Nasabah Baru">
        <x-slot:layar>
            <small>Nama</small>
            <b class="angka kecil" data-ikut="name" data-kosong="Belum diisi">Belum diisi</b>
            <div class="kisi" style="margin-top:12px">
                <div>
                    <small>Telepon</small>
                    <b class="angka kecil kuning" data-ikut="telepon" data-kosong="-">-</b>
                </div>
                <div>
                    <small>Domisili</small>
                    <b class="angka kecil kuning" data-ikut="kategori" data-peta="warga=Warga|umum=Umum" data-kosong="-">-</b>
                </div>
            </div>
            <p class="baca" data-ikut-alamat data-kosong="Alamat belum diisi">Alamat belum diisi</p>
        </x-slot:layar>

        <label for="name">
            Nama lengkap
            <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" maxlength="100" required>
            @error('name')<span class="err">{{ $message }}</span>@enderror
        </label>

        <label for="email">
            Email
            <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" maxlength="255" required>
            @error('email')<span class="err">{{ $message }}</span>@enderror
        </label>

        <label for="telepon">
            Telepon (angka / tanda +)
            <input id="telepon" name="telepon" type="tel" autocomplete="tel" value="{{ old('telepon') }}" maxlength="20" required>
            @error('telepon')<span class="err">{{ $message }}</span>@enderror
        </label>

        @include('partials.alamat-fields')

        <label for="password">
            Kata sandi awal (min. 8, kombinasi huruf &amp; angka)
            <input id="password" name="password" type="password" autocomplete="new-password" required>
            @error('password')<span class="err">{{ $message }}</span>@enderror
        </label>

        <div class="deret">
            <button class="key g besar" type="submit">Tambah nasabah</button>
            <a href="{{ route('admin.nasabah.index') }}" class="key p">Batal</a>
        </div>
    </x-alat>
</form>
@endsection
