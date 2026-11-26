@extends('layouts.app', ['title' => 'Profil Nasabah · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Profil Nasabah'])

@section('content')
<small class="kick">Profil</small>
<h1>Profil <em>Ibu</em></h1>
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
            <small>Email tidak dapat diubah dari halaman ini.</small>
        </label>

        <label for="telepon">
            Telepon / WhatsApp
            <input id="telepon" name="telepon" type="tel" autocomplete="tel" value="{{ old('telepon', $nasabah->telepon) }}" maxlength="20" pattern="[0-9+]+" data-validation-pattern-message="Gunakan angka dan tanda + saja. Hapus spasi atau tanda hubung." required autofocus>
            @error('telepon')<span class="err" data-validation-error-for="telepon">{{ $message }}</span>@enderror
        </label>

        <label for="alamat">
            Alamat domisili ({{ $nasabah->kategori === 'umum' ? 'Umum' : 'Warga Taman Gading' }})
            <input id="alamat" type="text" value="{{ $nasabah->alamat }}" readonly disabled>
            <small>Pengurus memeriksa alamat dan status domisili. Hubungi pengurus jika alamat Ibu berubah.</small>
        </label>

        <div class="deret">
            <button class="key g besar" type="submit">Simpan kontak</button>
        </div>
    </x-alat>
</form>

<form method="POST" action="{{ route('password.change.update') }}" novalidate>
    @csrf
    <x-alat tool="struk" plat="Ganti Kata Sandi">
        <label for="current_password" style="position:relative">
            Kata sandi saat ini
            <span class="pw-wrap" style="display:block;position:relative">
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" required style="padding-right:44px;width:100%;box-sizing:border-box">
                <button type="button" class="pw-eye" aria-label="Tampilkan kata sandi saat ini" aria-pressed="false" aria-controls="current_password" onclick="toggleProfilePassword('current_password', 'icon-profile-current-password', this, 'kata sandi saat ini')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:var(--mu)">
                    <svg class="i" id="icon-profile-current-password" style="width:20px;height:20px"><use href="#i-eye"/></svg>
                </button>
            </span>
            @error('current_password')<span class="err" data-validation-error-for="current_password">{{ $message }}</span>@enderror
        </label>

        <label for="password" style="position:relative">
            Kata sandi baru
            <span class="pw-wrap" style="display:block;position:relative">
                <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" pattern="(?=.*[A-Za-z])(?=.*[0-9]).+" data-validation-pattern-message="Tambahkan minimal satu huruf dan satu angka." required style="padding-right:44px;width:100%;box-sizing:border-box">
                <button type="button" class="pw-eye" aria-label="Tampilkan kata sandi baru" aria-pressed="false" aria-controls="password" onclick="toggleProfilePassword('password', 'icon-profile-new-password', this, 'kata sandi baru')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:var(--mu)">
                    <svg class="i" id="icon-profile-new-password" style="width:20px;height:20px"><use href="#i-eye"/></svg>
                </button>
            </span>
            <small>Minimal 8 karakter, kombinasi huruf dan angka.</small>
            @error('password')<span class="err" data-validation-error-for="password">{{ $message }}</span>@enderror
        </label>

        <label for="password_confirmation" style="position:relative">
            Ketik ulang kata sandi baru
            <span class="pw-wrap" style="display:block;position:relative">
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" data-match="password" required style="padding-right:44px;width:100%;box-sizing:border-box">
                <button type="button" class="pw-eye" aria-label="Tampilkan konfirmasi kata sandi" aria-pressed="false" aria-controls="password_confirmation" onclick="toggleProfilePassword('password_confirmation', 'icon-profile-confirm-password', this, 'konfirmasi kata sandi')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:var(--mu)">
                    <svg class="i" id="icon-profile-confirm-password" style="width:20px;height:20px"><use href="#i-eye"/></svg>
                </button>
            </span>
        </label>

        <div class="deret">
            <button class="key g besar" type="submit">Simpan kata sandi</button>
        </div>
        @if(session('password_change_success'))
            <div id="password-change-success" class="password-change-success" role="status" aria-live="polite" aria-atomic="true">
                <svg class="i" aria-hidden="true"><use href="#i-check"/></svg>
                <strong>{{ session('password_change_success') }}</strong>
            </div>
            <script>
                window.onAppPageReady(function () {
                    var message = document.getElementById('password-change-success');
                    if (!message) return;

                    window.setTimeout(function () {
                        message.classList.add('is-hiding');
                        window.setTimeout(function () {
                            message.remove();
                        }, 250);
                    }, 5000);
                });
            </script>
        @endif
    </x-alat>
</form>

<p class="sub" style="margin-top:24px">
    Data pribadi Ibu dilindungi sesuai UU No. 27 Tahun 2022. Baca <a class="lk" href="{{ route('privacy.policy') }}">Kebijakan Privasi</a>.
</p>

<script>
function toggleProfilePassword(inputId, iconId, button, label) {
    var input = document.getElementById(inputId);
    var icon = document.getElementById(iconId);
    if (!input || !icon || !button) return;

    var isVisible = input.type === 'password';
    input.type = isVisible ? 'text' : 'password';
    icon.querySelector('use').setAttribute('href', isVisible ? '#i-eye-off' : '#i-eye');
    button.setAttribute('aria-pressed', String(isVisible));
    button.setAttribute('aria-label', (isVisible ? 'Sembunyikan ' : 'Tampilkan ') + label);
}
</script>
@endsection
