@extends('layouts.app', ['title' => 'Profil Pengurus · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Profil Pengurus'])

@section('content')
<small class="kick">Profil pengurus</small>
<h1>Akun dan <em>aktivitas</em></h1>
<p class="lead">{{ $admin->name }} · {{ $admin->email }} · Hak akses: Pengurus</p>

<h2>Ganti kata sandi akun</h2>
<form class="f" method="POST" action="{{ route('password.change.update') }}" style="max-width:520px;margin-bottom:30px" novalidate>
    @csrf

    <label for="current_password">
        Kata sandi saat ini
        <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
        @error('current_password')<span class="err" data-validation-error-for="current_password" style="display:block">{{ $message }}</span>@enderror
    </label>

    <label for="password">
        Kata sandi baru (minimal 8 karakter, berisi huruf dan angka)
        <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" pattern="(?=.*[A-Za-z])(?=.*[0-9]).+" data-validation-pattern-message="Tambahkan minimal satu huruf dan satu angka." required>
        @error('password')<span class="err" data-validation-error-for="password" style="display:block">{{ $message }}</span>@enderror
    </label>

    <label for="password_confirmation">
        Ketik ulang kata sandi baru
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" data-match="password" required>
    </label>

    <button class="btn" type="submit">Ganti kata sandi</button>
</form>

<h2>Riwayat aktivitas</h2>
<form class="f frow" method="GET" action="{{ route('admin.profile.show') }}" style="margin-bottom:16px">
    <label>
        Cari
        <input name="q" type="search" placeholder="Cari aktivitas, nama, atau alamat IP..." value="{{ request('q') }}">
    </label>
    <button class="btn sm" type="submit">Cari</button>
    @if(request('q'))
        <a href="{{ route('admin.profile.show') }}" class="btn o sm" style="text-decoration:none">Reset</a>
    @endif
</form>

<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Pengguna</th>
                <th>Aksi</th>
                <th>Data terkait</th>
                <th>Alamat IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $log->user->name ?? 'Sistem atau tamu' }}</td>
                    <td><code>{{ $log->aksi }}</code></td>
                    <td>
                        {{ $log->objek_tipe ? $log->objek_tipe . ' #' . $log->objek_id : '-' }}
                        @if($log->keterangan)
                            <small style="color:var(--mu);display:block">
                                {{ is_array($log->keterangan) ? json_encode($log->keterangan, JSON_UNESCAPED_UNICODE) : $log->keterangan }}
                            </small>
                        @endif
                    </td>
                    <td><code>{{ $log->ip_address ?? '-' }}</code></td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada catatan log aktivitas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">
    {{ $logs->links() }}
</div>
@endsection
