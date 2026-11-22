@extends('layouts.app', ['title' => 'Katalog Harga Sampah · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Katalog Harga Sampah'])

@section('content')
<small class="kick">Katalog</small>
<h1>Harga sampah <em>hari ini</em></h1>
<p class="lead">Daftar harga pembelian sampah per kg yang berlaku di Bank Sampah Sahabat Ibu.</p>

<div class="tbl">
    <table>
        <thead>
            <tr>
                <th>Jenis Sampah</th>
                <th>Satuan</th>
                <th>Harga Berlaku Saat Ini</th>
            </tr>
        </thead>
        <tbody>
            @forelse($katalog as $item)
                <tr>
                    <td><b>{{ $item['nama'] }}</b></td>
                    <td>{{ $item['satuan'] }}</td>
                    <td>
                        @if($item['harga_per_kg'] > 0)
                            <b>Rp {{ number_format($item['harga_per_kg'], 0, ',', '.') }}</b> / {{ $item['satuan'] }}
                        @else
                            <span style="color:var(--mu)">Belum ditetapkan</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">Katalog harga sampah belum tersedia.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<p class="lead" style="margin-top:20px;font-size:13px">
    Harga dapat diperbarui oleh pengurus secara berkala sesuai dinamika harga pasar pengepul. Nilai yang digunakan untuk transaksi setoran Ibu selalu mengacu pada harga yang berlaku pada hari setoran ditimbang.
</p>
@endsection
