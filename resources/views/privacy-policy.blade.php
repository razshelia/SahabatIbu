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
    <p class="lead">Persetujuan eksplisit Ibu saat mendaftar. Bukti persetujuan (nilai dan versi kebijakan) dicatat.</p>

    <h2>Siapa yang bisa melihat data</h2>
    <ul>
        <li>Nasabah hanya melihat datanya sendiri</li>
        <li>Pengurus hanya melihat data operasional</li>
        <li>Laporan yang diekspor tanpa data kontak</li>
    </ul>

    <h2>Penyimpanan dan penghapusan</h2>
    <p class="lead">
        Akun tidak dihapus agar arsip keuangan utuh. Atas permohonan, pengurus menonaktifkan lalu menganonimkan data pribadi Ibu (nama, telepon, alamat, email, ID Google, IP). Anonimisasi ditolak jika saldo belum nol atau masih ada penarikan berjalan.
    </p>

    <h2>Hak Ibu</h2>
    <ul>
        <li>Mendapat informasi (halaman ini)</li>
        <li>Mengakses dan memperbarui data lewat Profil</li>
        <li>Menarik persetujuan lewat permohonan ke pengurus</li>
    </ul>

    <p class="sub"><a class="lk" href="{{ url()->previous() ?: url('/') }}">Kembali</a></p>
</article>
@endsection
