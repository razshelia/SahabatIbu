<?php

/*
| Pengaturan khusus Bank Sampah Sahabat Ibu.
|
| env() hanya boleh dibaca di berkas config. Setelah `php artisan config:cache`, pemanggilan env()
| di luar config selalu mengembalikan null, jadi seeder membaca nilai ini lewat config().
*/

return [
    // Akun admin pertama, dipakai AdminSeeder (BR-14: admin hanya dibuat lewat seeder).
    'admin' => [
        'nama'     => env('ADMIN_NAME', 'Admin Bank Sampah'),
        'email'    => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
        // true = jika admin dengan email ini sudah ada, kata sandinya diganti ke ADMIN_PASSWORD
        // dan wajib diganti lagi saat login. Biarkan false kecuali memang lupa kata sandi admin.
        'reset_password' => (bool) env('ADMIN_RESET_PASSWORD', false),
    ],
];
