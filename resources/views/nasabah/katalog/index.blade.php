@extends('layouts.app', ['title' => 'Katalog Harga Sampah · Bank Sampah Sahabat Ibu', 'dataView' => 'home', 'pageTitle' => 'Katalog Harga Sampah'])

@section('content')
<small class="kick">Katalog</small>
<h1>Harga sampah <em>hari ini</em></h1>
<p class="lead">Berikut harga yang berlaku untuk setiap kilogram sampah.</p>

<x-alat tool="rak" plat="Daftar Harga">
    <x-slot:layar>
        <small>Harga beli per kg, berlaku hari ini</small>
        @if(count($katalog))
            <div class="daftar">
                @foreach($katalog as $item)
                    @php($harga = $item->harga_berlaku?->harga_per_kg)
                    <div>
                        <span style="color:#eef5f0">{{ $item['nama'] }}</span>
                        @if($harga !== null && $harga > 0)
                            <span>Rp {{ number_format((float) $harga, 0, ',', '.') }}/{{ $item['satuan'] }}</span>
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
    <p style="margin:0;font-weight:700;font-size:17px">Nilai setoran dihitung dari berat sampah dan harga yang berlaku.</p>
</x-alat>

<p class="lead" style="margin-top:20px;font-size:15px">
    Harga dapat berubah sewaktu-waktu. Setoran dihitung menggunakan harga yang berlaku saat sampah ditimbang.
</p>
@endsection
