<?php

return [
    // Nomor WhatsApp admin. Isi di .env, contoh: ADMIN_WHATSAPP=081234567890
    // (awalan 0 otomatis diubah jadi 62; spasi, tanda + atau strip diabaikan).
    'whatsapp' => env('ADMIN_WHATSAPP', ''),
    'whatsapp_pesan' => env('ADMIN_WHATSAPP_PESAN', 'Halo pengurus Bank Sampah Sahabat Ibu, saya ingin bertanya.'),
];
