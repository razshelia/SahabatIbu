@extends('layouts.app', ['title' => 'Ubah Data Nasabah · Bank Sampah Sahabat Ibu', 'dataView' => 'scan', 'pageTitle' => 'Ubah Nasabah'])

@section('content')
<small class="kick">Pendaftar &amp; nasabah</small>
<h1>Ubah data <em>{{ $nasabah->user->name }}</em></h1>
<p class="lead">Mengubah data ini tidak mengubah saldo atau riwayat transaksi.</p>

<form method="POST" action="{{ route('admin.nasabah.update', $nasabah) }}" novalidate>
    @csrf
    @method('PUT')

    <x-alat tool="scan" plat="Kartu Nasabah">
        <x-slot:layar>
            <div class="baris">
                <div>
                    <small>Nomor nasabah</small>
                    <b class="angka kecil">{{ $nasabah->nomor_nasabah }}</b>
                </div>
                <div class="kanan">
                    <small>Domisili</small>
                    <b class="angka kecil kuning" data-ikut="kategori" data-peta="warga=Warga|umum=Umum" data-kosong="-">-</b>
                </div>
            </div>
            <small style="margin-top:12px">Nama</small>
            <b class="angka kecil" data-ikut="name" data-kosong="Belum diisi">Belum diisi</b>
            <p class="baca" data-ikut-alamat data-kosong="Alamat belum diisi">Alamat belum diisi</p>
        </x-slot:layar>

        <label for="name">
            Nama lengkap
            <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name', $nasabah->user->name) }}" maxlength="100" required>
            @error('name')<span class="err" data-validation-error-for="name">{{ $message }}</span>@enderror
        </label>

        <label for="email">
            Email
            <input id="email" type="email" value="{{ $nasabah->user->email }}" readonly disabled>
            <small>Email akun tidak dapat diubah dari halaman ini.</small>
        </label>

        <label for="telepon">
            Telepon
            <input id="telepon" name="telepon" type="tel" autocomplete="tel" value="{{ old('telepon', $nasabah->telepon) }}" maxlength="20" pattern="[0-9+]+" data-validation-pattern-message="Gunakan angka dan tanda + saja. Hapus spasi atau tanda hubung." required>
            @error('telepon')<span class="err" data-validation-error-for="telepon">{{ $message }}</span>@enderror
        </label>

        @include('partials.alamat-fields', ['nasabah' => $nasabah])

        <div class="deret">
            <button class="key g besar" type="submit">Simpan perubahan</button>
            <a href="{{ route('admin.nasabah.index') }}" class="key p">Batal</a>
        </div>
    </x-alat>
</form>

<p class="sub" style="margin-top:24px"><a class="lk" href="{{ route('admin.nasabah.index') }}">&larr; Kembali ke daftar nasabah</a></p>
@endsection
