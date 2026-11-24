@extends('layouts.guest', ['title' => 'Kebijakan Privasi · Bank Sampah Sahabat Ibu', 'dataView' => 'riwayat'])

@section('content')
<article class="box w g">
    <small class="kick">Halaman publik</small>
    <h1>Kebijakan <em>Privasi</em></h1>
    <small class="v">Versi 1.0 · sesuai UU No. 27 Tahun 2022</small>

    <h2>Data yang kami proses</h2>
    <ul>
        <li>Nama, email, telepon, dan alamat</li>
        <li>ID Google (jika masuk dengan Google) dan kata sandi dalam bentuk hash</li>
        <li>Saldo, riwayat setoran dan penarikan</li>
        <li>Log aktivitas: waktu, aksi, alamat IP, dan perangkat</li>
    </ul>

    <h2>Dasar pemrosesan</h2>
    <p class="lead">Kami memproses data setelah Ibu menyetujuinya saat mendaftar. Versi kebijakan yang disetujui juga kami catat.</p>

    <h2>Siapa yang bisa melihat data</h2>
    <ul>
        <li>Nasabah hanya dapat melihat data miliknya sendiri</li>
        <li>Pengurus dapat melihat data yang diperlukan untuk mengelola bank sampah</li>
        <li>Laporan yang diunduh tidak memuat data kontak</li>
    </ul>

    <h2>Penyimpanan dan penghapusan</h2>
    <p class="lead">
        Akun tidak dihapus agar catatan keuangan tetap utuh. Jika Ibu mengajukan permintaan, pengurus dapat menonaktifkan akun lalu menganonimkan data pribadi, seperti nama, telepon, alamat, email, ID Google, dan alamat IP. Data baru dapat dianonimkan setelah saldo nol dan tidak ada pengajuan penarikan yang masih diproses.
    </p>

    <h2>Hak Ibu</h2>
    <ul>
        <li>Mendapat informasi tentang penggunaan data</li>
        <li>Melihat dan memperbarui data melalui halaman Profil</li>
        <li>Mengajukan penarikan persetujuan kepada pengurus</li>
    </ul>

    <p class="sub"><a class="lk" href="{{ url()->previous() ?: url('/') }}">Kembali</a></p>
</article>
@endsection
