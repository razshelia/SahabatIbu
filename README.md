# Bank Sampah Sahabat Ibu

Aplikasi web Bank Sampah Sahabat Ibu (Perumahan Taman Gading, Jember). Laravel 13, PHP 8.3, MySQL, Blade + Tailwind.

## Menjalankan di komputer sendiri

```bash
composer install
cp .env.example .env          # lalu isi DB_*, ADMIN_EMAIL, dan ADMIN_PASSWORD di .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed           # admin pertama + jenis sampah + harga awal
```

Ekstensi PHP `bcmath` harus aktif (perhitungan saldo memakainya).

### Data awal (`php artisan db:seed`)

| Seeder | Isi | Catatan |
|---|---|---|
| `AdminSeeder` | Akun admin dari `ADMIN_EMAIL` / `ADMIN_PASSWORD` | Wajib ganti kata sandi saat login pertama. Dijalankan ulang tidak menimpa admin yang sudah ada. |
| `JenisSampahSeeder` | Kertas, Botol Plastik, Logam | Aman dijalankan ulang. |
| `HargaSampahSeeder` | Harga awal **contoh** | Hanya mengisi jenis yang belum punya harga, tidak pernah menimpa harga yang sudah ditetapkan pengurus. |

Nilai `ADMIN_*` dan batas penarikan kategori umum (`BATAS_PENARIKAN_UMUM`, default Rp100.000; nilai `0` menonaktifkan batas)
dibaca lewat `config/bank_sampah.php`, jadi tetap bekerja setelah `php artisan config:cache`
(jalankan `php artisan config:clear` bila baru mengubah `.env`).

### Data dummy untuk pengujian (bertema K-pop)

```bash
php artisan db:seed --class=DemoSeeder
```

Mengisi 24 nasabah (BLACKPINK, BTS, NewJeans, aespa, Stray Kids, TWICE), 140 setoran selama sekitar 70 hari
dengan riwayat harga naik/turun, dan 16 pengajuan penarikan dengan semua status. Seluruh data dibuat lewat
service aplikasi sehingga saldo, saldo ditahan, kode transaksi, dan log aktivitas konsisten.
Aman dijalankan ulang (data yang sudah ada dilewati) dan **menolak berjalan di production**.

- Kata sandi semua akun demo: `Password123!`. Contoh: `jungkook.bts@example.test` (aktif, banyak riwayat),
  `hyein.newjeans@example.test` (menunggu verifikasi), `seokjin.bts@example.test` (nonaktif, saldo 0, bisa dianonimkan),
  `yoongi.bts@example.test` (nonaktif, masih bersaldo), `felix.straykids@example.test` (nasabah umum).
- Hanya sebagian: `--class=NasabahDemoSeeder`, `--class=SetoranDemoSeeder`, `--class=PenarikanDemoSeeder`
  (urutannya harus nasabah, lalu setoran, lalu penarikan).
- Mulai dari nol: `php artisan migrate:fresh --seed && php artisan db:seed --class=DemoSeeder`.

## Pengujian

```bash
php artisan test
```

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
