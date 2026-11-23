{{--
    Badan alat: bungkus form/isi halaman supaya tampil sebagai alat yang sama dengan di ruangan 3D.
    Pemakaian:
        <x-alat tool="capit" plat="Ambil Uang">
            <x-slot:layar> ...isi layar LCD... </x-slot:layar>
            ...isian dan tombol (slot utama)...
            <x-slot:nampan> ...keterangan di nampan bawah... </x-slot:nampan>
        </x-alat>
    tool : scan | rak | timbang | struk | riwayat | capit  (kunci yang sama dengan alat di latar.js / GarasiData)
    plat : tulisan di papan nama (samakan dengan label alat di ruangan)
--}}
@props(['tool', 'plat'])
<section {{ $attributes->merge(['class' => 'alat', 'data-alat' => $tool, 'aria-label' => $plat]) }}>
    <header class="alat-plat">
        <h2>{{ $plat }}</h2>
        <span class="sambung">Layar alat di ruangan ikut berubah</span>
    </header>
    @isset($layar)
        <div class="layar" aria-live="polite">{{ $layar }}</div>
    @endisset
    <div class="alat-badan">{{ $slot }}</div>
    @isset($nampan)
        <div class="nampan">{{ $nampan }}</div>
    @endisset
</section>
