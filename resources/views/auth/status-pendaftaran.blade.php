@extends('layouts.guest', ['title' => 'Status pendaftaran · Bank Sampah Sahabat Ibu', 'dataView' => 'scan'])

@section('content')
<section class="box g">
    <small class="kick">Status pendaftaran</small>

    @if($user->status === 'menunggu')
        <h1>Menunggu Verifikasi</h1>
        <p class="lead">Halo, <b>{{ $user->name }}</b> ({{ $user->email }}).</p>
        <span class="pill">Menunggu Verifikasi</span>

        <h2>Sementara menunggu, Ibu bisa:</h2>
        <ul>
            <li>Menghubungi pengurus bila ada kendala</li>
            <li>Melengkapi data atau mengganti kata sandi</li>
            <li>Melihat status pendaftaran ini, lalu keluar</li>
        </ul>

        <p class="lead" style="margin-top:14px">
            Setoran dan penarikan baru bisa dilakukan setelah pengurus menyetujui dan akun berstatus <em>Aktif</em>.
        </p>
    @elseif($user->status === 'ditolak')
        <h1>Pendaftaran Ditolak</h1>
        <p class="lead">Halo, <b>{{ $user->name }}</b> ({{ $user->email }}).</p>
        <span class="pill no">Ditolak</span>

        @if($user->alasan_penolakan)
            <div style="background:rgba(239,68,68,0.15);border:1px solid #ef4444;padding:12px;border-radius:12px;margin:14px 0;font-size:13px;color:#fca5a5;">
                <b>Alasan penolakan:</b><br>
                {{ $user->alasan_penolakan }}
            </div>
        @endif

        <p class="lead" style="margin-top:14px">
            Silakan hubungi pengurus RT/RW jika memerlukan klarifikasi data pendaftaran Ibu.
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
