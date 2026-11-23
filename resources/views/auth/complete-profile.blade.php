@extends('layouts.guest', ['title' => 'Lengkapi data · Bank Sampah Sahabat Ibu', 'dataView' => 'scan'])

@section('content')
<section class="box g">
    <small class="kick">Akun Google</small>
    <h1>Lengkapi <em>data Ibu</em></h1>
    <p class="lead">Tinggal sedikit lagi, <b>{{ $user->name }}</b>. Data ini dipakai pengurus untuk verifikasi.</p>

    <form class="f" method="POST" action="{{ route('profile.complete.store') }}" novalidate>
        @csrf

        <label for="telepon">
            Telepon
            <input id="telepon" name="telepon" type="tel" autocomplete="tel" value="{{ old('telepon') }}" maxlength="20" required autofocus>
            @error('telepon')<span class="err" style="display:block">{{ $message }}</span>@enderror
        </label>

        @include('partials.alamat-fields')

        <label class="ck" for="persetujuan_privasi">
            <input id="persetujuan_privasi" name="persetujuan_privasi" type="checkbox" value="1" {{ old('persetujuan_privasi') ? 'checked' : '' }} required>
            <span>Saya menyetujui pemrosesan data pribadi saya sesuai <a class="lk" href="{{ route('privacy.policy') }}" target="_blank" rel="noopener">Kebijakan Privasi</a> (UU No. 27 Tahun 2022).</span>
        </label>
        @error('persetujuan_privasi')<span class="err" style="display:block">{{ $message }}</span>@enderror

        <button class="btn" type="submit">Simpan dan lanjut</button>
    </form>
</section>
@endsection
