@extends('layouts.app', ['title' => 'Katalog Harga Sampah · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Katalog Harga Sampah'])

@section('content')
<small class="kick">Katalog</small>
<h1>Harga sampah <em>hari ini</em></h1>
<p class="lead">Daftar harga pembelian sampah per kg yang berlaku di Bank Sampah Sahabat Ibu.</p>

<x-alat tool="rak" plat="Daftar Harga">
    <x-slot:layar>
        <small>Harga beli per kg, berlaku hari ini</small>
        @if(count($katalog))
            <div class="daftar">
                @foreach($katalog as $item)
                    <div>
                        <span style="color:#eef5f0">{{ $item['nama'] }}</span>
                        @if($item['harga_per_kg'] > 0)
                            <span>Rp {{ number_format($item['harga_per_kg'], 0, ',', '.') }}/{{ $item['satuan'] }}</span>
                        @else
                            <span class="kuning" style="color:#f3c64f">Belum ditetapkan</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <b class="angka kecil kuning">Harga belum tersedia</b>
        @endif
    </x-slot:layar>
    <p style="margin:0;font-weight:700;font-size:17px">Timbang dulu, nilainya dihitung dari harga di atas.</p>
</x-alat>

<p class="lead" style="margin-top:20px;font-size:15px">
    Harga dapat diperbarui oleh pengurus secara berkala sesuai dinamika harga pasar pengepul. Nilai yang digunakan untuk transaksi setoran Ibu selalu mengacu pada harga yang berlaku pada hari setoran ditimbang.
</p>
@endsection
