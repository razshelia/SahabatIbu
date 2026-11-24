@extends('layouts.guest', ['title' => 'Status pendaftaran · Bank Sampah Sahabat Ibu', 'dataView' => 'scan'])

@section('content')
<section class="box g">
    <small class="kick">Status pendaftaran</small>

    @if($user->status === 'menunggu')
        <h1>Pendaftaran sedang diperiksa</h1>
        <p class="lead">Halo, <b>{{ $user->name }}</b> ({{ $user->email }}).</p>
        <span class="pill">Menunggu</span>

        <h2>Sambil menunggu, Ibu bisa:</h2>
        <ul>
            <li>Menghubungi pengurus jika perlu bantuan</li>
            <li>Melengkapi data atau mengganti kata sandi</li>
            <li>Keluar dari akun dan kembali lagi nanti untuk melihat status</li>
        </ul>

        <p class="lead" style="margin-top:14px">
            Ibu bisa mulai menyetor dan mengajukan penarikan setelah akun disetujui.
        </p>
    @elseif($user->status === 'ditolak')
        <h1>Pendaftaran belum disetujui</h1>
        <p class="lead">Halo, <b>{{ $user->name }}</b> ({{ $user->email }}).</p>
        <span class="pill no">Ditolak</span>

        @if($user->alasan_penolakan)
            <div style="background:rgba(239,68,68,0.15);border:1px solid #ef4444;padding:12px;border-radius:12px;margin:14px 0;font-size:13px;color:#fca5a5;">
                <b>Catatan dari pengurus:</b><br>
                {{ $user->alasan_penolakan }}
            </div>
        @endif

        <p class="lead" style="margin-top:14px">
            Hubungi pengurus RT/RW jika Ibu ingin menanyakan keputusan ini.
        </p>
    @else
        <h1>Akun {{ ucfirst($user->status) }}</h1>
        <p class="lead">Halo, <b>{{ $user->name }}</b>.</p>
        <span class="pill {{ $user->status === 'aktif' ? 'ok' : 'no' }}">{{ ucfirst($user->status) }}</span>
    @endif

    <div class="row" style="margin-top:20px">
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button class="btn o" type="submit">Keluar</button>
        </form>
    </div>
</section>
@endsection
