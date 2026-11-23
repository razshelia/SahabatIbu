@extends('layouts.app', ['title' => 'Profil Nasabah · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Profil Nasabah'])

@section('content')
<small class="kick">Profil</small>
<h1>Data <em>Ibu</em></h1>
<p class="lead">
    <b>{{ $user->name }}</b> · {{ $nasabah->nomor_nasabah }} · {{ $user->email }}
    <span class="pill ok" style="margin-left:8px">{{ ucfirst($user->status) }}</span>
</p>

<form method="POST" action="{{ route('nasabah.profile.update') }}" novalidate>
    @csrf
    @method('PUT')
    <x-alat tool="struk" plat="Profil Saya">
        <x-slot:layar>
            <small>Nama</small>
            <b class="angka kecil">{{ $user->name }}</b>
            <div class="kisi" style="margin-top:12px">
                <div>
                    <small>Nomor nasabah</small>
                    <b class="angka kecil kuning">{{ $nasabah->nomor_nasabah }}</b>
                </div>
                <div>
                    <small>Telepon / WhatsApp</small>
                    <b class="angka kecil kuning" data-ikut="telepon" data-kosong="-">{{ $nasabah->telepon }}</b>
                </div>
            </div>
        </x-slot:layar>

        <label for="name">
            Nama
            <input id="name" type="text" value="{{ $user->name }}" readonly disabled>
            <small>Perubahan nama silakan hubungi pengurus.</small>
        </label>

        <label for="email">
            Email
            <input id="email" type="email" value="{{ $user->email }}" readonly disabled>
            <small>Email akun terikat dan dilindungi.</small>
        </label>

        <label for="telepon">
            Telepon / WhatsApp
            <input id="telepon" name="telepon" type="tel" autocomplete="tel" value="{{ old('telepon', $nasabah->telepon) }}" maxlength="20" required autofocus>
            @error('telepon')<span class="err">{{ $message }}</span>@enderror
        </label>

        <label for="alamat">
            Alamat domisili ({{ $nasabah->kategori === 'umum' ? 'Umum' : 'Warga Taman Gading' }})
            <input id="alamat" type="text" value="{{ $nasabah->alamat }}" readonly disabled>
            <small>Alamat dan status domisili diverifikasi pengurus. Untuk pindah alamat, hubungi pengurus.</small>
        </label>

        <div class="deret">
            <button class="key g besar" type="submit">Simpan kontak</button>
        </div>
    </x-alat>
</form>

<form method="POST" action="{{ route('password.change.update') }}" novalidate>
    @csrf
    <x-alat tool="struk" plat="Ganti Kata Sandi">
        <label for="current_password">
            Kata sandi saat ini
            <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
            @error('current_password')<span class="err">{{ $message }}</span>@enderror
        </label>

        <label for="password">
            Kata sandi baru
            <input id="password" name="password" type="password" autocomplete="new-password" required>
            <small>Minimal 8 karakter, kombinasi huruf dan angka.</small>
            @error('password')<span class="err">{{ $message }}</span>@enderror
        </label>

        <label for="password_confirmation">
            Ulangi kata sandi baru
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        </label>

        <div class="deret">
            <button class="key g besar" type="submit">Simpan kata sandi</button>
        </div>
    </x-alat>
</form>

<p class="sub" style="margin-top:24px">
    Pelindungan data Ibu dijamin UU No. 27 Tahun 2022. Baca <a class="lk" href="{{ route('privacy.policy') }}">Kebijakan Privasi</a>.
</p>
@endsection
