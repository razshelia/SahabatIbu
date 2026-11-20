@extends('layouts.guest')

@section('content')
<div class="bg-white p-6 rounded-lg shadow border border-gray-200 prose prose-sm max-w-none text-left">
    <h1 class="text-xl font-bold text-gray-800 mb-2">Kebijakan Privasi</h1>
    <p class="text-xs text-gray-500 mb-4">Terakhir diperbarui: 20 November 2026</p>

    <div class="space-y-4 text-xs text-gray-700 leading-relaxed">
        <section>
            <h2 class="text-sm font-bold text-gray-800">1. Pendahuluan</h2>
            <p>Bank Sampah Sahabat Ibu (Perumahan Taman Gading, Jember) berkomitmen untuk melindungi data pribadi warga dan nasabah kami sesuai dengan Undang-Undang No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP).</p>
        </section>

        <section>
            <h2 class="text-sm font-bold text-gray-800">2. Data Pribadi yang Kami Kumpulkan</h2>
            <ul class="list-disc pl-4 space-y-1">
                <li><strong>Identitas & Kontak:</strong> Nama lengkap, alamat tempat tinggal, nomor telepon/WhatsApp, dan alamat email.</li>
                <li><strong>Autentikasi:</strong> Kata sandi (di-hash kuat dengan algoritma Bcrypt/Argon2) atau pengenal unik Google ID (bagi pengguna yang masuk menggunakan Google OAuth).</li>
                <li><strong>Keuangan & Operasional:</strong> Saldo tabungan, data setoran sampah (berat dan nilai), dan riwayat penarikan dana tunai.</li>
                <li><strong>Log Teknis:</strong> Alamat IP dan informasi peramban (User Agent) saat login dan transaksi untuk audit keamanan.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-sm font-bold text-gray-800">3. Tujuan Pemrosesan Data</h2>
            <p>Data pribadi digunakan semata-mata untuk:</p>
            <ul class="list-disc pl-4 space-y-1">
                <li>Pencatatan tabungan sampah dan verifikasi kepemilikan rekening nasabah.</li>
                <li>Penyaluran dan konfirmasi penarikan saldo secara tatap muka.</li>
                <li>Keamanan akun dan pelacakan audit transaksi operasional bank sampah.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-sm font-bold text-gray-800">4. Pembatasan dan Kerahasiaan</h2>
            <p>Setiap nasabah hanya memiliki hak akses untuk melihat data tabungan miliknya sendiri. Pengurus tidak membagikan atau mempublikasikan spreadsheet berisikan rincian saldo warga ke grup publik.</p>
        </section>

        <section>
            <h2 class="text-sm font-bold text-gray-800">5. Hak Subjek Data & Anonimisasi</h2>
            <p>Nasabah berhak memperbarui kontak profil secara mandiri. Jika nasabah ingin berhenti dan menghapus data pribadinya, pengurus akan melakukan prosedur <strong>anonimisasi data</strong> (nama, email, telepon, dan alamat diganti kode acak anonim) setelah saldo tersisa bernilai nol dan tidak ada penarikan tertunda, demi menjaga keutuhan catatan arsip pembukuan kas.</p>
        </section>
    </div>

    <div class="mt-6 pt-4 border-t border-gray-200 text-center">
        <a href="{{ url()->previous() ?: route('login') }}" class="text-xs text-emerald-700 font-semibold hover:underline">&larr; Kembali</a>
    </div>
</div>
@endsection
