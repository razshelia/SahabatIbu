@extends('layouts.app', ['title' => 'Katalog Harga · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Katalog Harga Sampah'])

@section('content')
<small class="kick">Katalog harga</small>
<h1>Harga sampah <em>harian</em></h1>
<p class="lead">Atur pembaruan harga per kg yang berlaku harian (BR-03, BR-04).</p>

<h2>Atur harga baru</h2>
<form class="f frow" method="POST" action="{{ route('admin.katalog.harga.store') }}" style="margin-bottom:24px">
    @csrf
    <label>
        Jenis Sampah
        <select name="jenis_sampah_id" required>
            @foreach($jenisList as $j)
                <option value="{{ $j->id }}">{{ $j->nama }} ({{ $j->satuan }})</option>
            @endforeach
        </select>
    </label>
    @include('partials.rupiah-input', ['id' => 'harga_per_kg', 'name' => 'harga_per_kg', 'label' => 'Harga/kg (Rp)', 'step' => 500, 'placeholder' => 'Contoh: 3000'])
    <label>
        Berlaku Mulai
        <input name="berlaku_mulai" type="date" required value="{{ date('Y-m-d') }}">
    </label>
    <button class="btn sm" type="submit">Simpan Harga</button>
</form>

<h2>Harga yang berlaku saat ini</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Jenis Sampah</th>
                <th>Satuan</th>
                <th>Harga Berlaku</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jenisList as $j)
                <tr>
                    <td><b>{{ $j->nama }}</b></td>
                    <td>{{ $j->satuan }}</td>
                    <td>
                        @if($j->harga_berlaku)
                            Rp {{ number_format($j->harga_berlaku->harga_per_kg, 0, ',', '.') }}/{{ $j->satuan }}
                            <small style="color:var(--mu);display:block">sejak {{ $j->harga_berlaku->berlaku_mulai->format('d/m/Y') }}</small>
                        @else
                            <span style="color:#f87171">Belum diatur</span>
                        @endif
                    </td>
                    <td>
                        <span class="pill {{ $j->aktif ? 'ok' : 'no' }}">
                            {{ $j->aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.katalog.jenis.update', $j) }}" style="display:inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="nama" value="{{ $j->nama }}">
                            <input type="hidden" name="satuan" value="{{ $j->satuan }}">
                            @if(!$j->aktif)
                                <input type="hidden" name="aktif" value="1">
                                <button class="btn o sm" type="submit">Aktifkan</button>
                            @else
                                <button class="btn o sm" type="submit">Nonaktifkan</button>
                            @endif
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada jenis sampah.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<h2>Riwayat perubahan harga</h2>
<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Jenis Sampah</th>
                <th>Harga per Satuan</th>
                <th>Berlaku Mulai</th>
                <th>Dibuat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayatHargaTerbaru as $rh)
                <tr>
                    <td><b>{{ $rh->jenisSampah->nama ?? '-' }}</b></td>
                    <td>Rp {{ number_format($rh->harga_per_kg, 0, ',', '.') }} / {{ $rh->jenisSampah->satuan ?? 'kg' }}</td>
                    <td>{{ $rh->berlaku_mulai->format('d/m/Y') }}</td>
                    <td>{{ $rh->pembuat->name ?? 'Admin' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada riwayat perubahan harga.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
