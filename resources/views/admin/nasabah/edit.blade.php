@extends('layouts.app', ['title' => 'Ubah Data Nasabah · Bank Sampah Sahabat Ibu', 'dataView' => 'scan', 'pageTitle' => 'Ubah Nasabah'])

@section('content')
<small class="kick">Pendaftar &amp; nasabah</small>
<h1>Ubah data <em>{{ $nasabah->user->name }}</em></h1>
<p class="lead">{{ $nasabah->nomor_nasabah }} · Saldo dan riwayat transaksi tidak ikut berubah.</p>

<form class="f" method="POST" action="{{ route('admin.nasabah.update', $nasabah) }}" style="max-width:560px" novalidate>
    @csrf
    @method('PUT')

    <label for="name">
        Nama lengkap
        <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name', $nasabah->user->name) }}" maxlength="100" required>
        @error('name')<span class="err" style="display:block">{{ $message }}</span>@enderror
    </label>

    <label for="email">
        Email
        <input id="email" type="email" value="{{ $nasabah->user->email }}" readonly disabled style="opacity:0.6;cursor:not-allowed">
        <small style="color:var(--mu);margin-top:2px;display:block">Email akun tidak dapat diubah sembarangan.</small>
    </label>

    <label for="telepon">
        Telepon
        <input id="telepon" name="telepon" type="tel" autocomplete="tel" value="{{ old('telepon', $nasabah->telepon) }}" maxlength="20" required>
        @error('telepon')<span class="err" style="display:block">{{ $message }}</span>@enderror
    </label>

    @include('partials.alamat-fields', ['nasabah' => $nasabah])

    <div class="row" style="margin-top:16px;gap:8px">
        <button class="btn" type="submit">Simpan Perubahan</button>
        <a href="{{ route('admin.nasabah.index') }}" class="btn o" style="text-decoration:none">Batal</a>
    </div>
</form>

<p class="sub" style="margin-top:24px"><a class="lk" href="{{ route('admin.nasabah.index') }}">&larr; Kembali ke daftar nasabah</a></p>
@endsection
