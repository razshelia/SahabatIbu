<?php

namespace Database\Seeders\Support;

/**
 * Daftar nasabah dummy bertema K-pop untuk pengujian (hanya dipakai seeder demo).
 *
 * Nama = nama depan/panggung + nama belakang Indonesia (karangan), grup ada di email
 * supaya mudah dikenali saat menguji, mis. jisoo.blackpink@example.test.
 * Semua akun demo memakai kata sandi DemoRoster::PASSWORD.
 *
 * Susunan sengaja mencakup semua keadaan yang perlu diuji:
 *  - aktif (banyak, riwayat setoran dan penarikan beragam)
 *  - menunggu verifikasi, ditolak, nonaktif (saldo nol -> bisa dianonimkan; saldo masih ada -> ditolak anonimisasi)
 *  - warga Taman Gading (blok + nomor rumah) dan umum (alamat bebas)
 *  - didaftarkan admin langsung vs daftar mandiri lalu diverifikasi
 */
final class DemoRoster
{
    public const PASSWORD = 'Password123!';

    /**
     * @return list<array{grup:string,nama:string,email:string,status:string,cara:string,kategori:string,
     *                    telepon:string,blok:?string,nomor_rumah:?string,alamat:?string,setoran:int,
     *                    berhenti:bool,alasan:?string,hari_daftar:int}>
     */
    public static function nasabah(): array
    {
        $warga = 0;
        $urut = 0;
        $out = [];

        $tambah = function (string $grup, string $nama, string $status, string $cara, string $kategori, int $setoran, array $opsi = []) use (&$out, &$warga, &$urut): void {
            $urut++;
            $depan = strtolower(explode(' ', $nama)[0]);
            $telepon = sprintf('0812%08d', 41000000 + $urut * 7919);
            if ($urut % 6 === 0) {
                $telepon = '+62' . substr($telepon, 1);
            }

            $blok = $nomor = $alamat = null;
            if ($kategori === 'warga') {
                $blok = chr(65 + $warga % 5);          // A-E
                $nomor = (string) (2 + $warga * 3);    // pasangan blok+nomor tidak pernah kembar
                $warga++;
            } else {
                $alamat = $opsi['alamat'];
            }

            $out[] = [
                'grup'        => $grup,
                'nama'        => $nama,
                'email'       => $depan . '.' . strtolower(str_replace(' ', '', $grup)) . '@example.test',
                'status'      => $status,
                'cara'        => $cara,
                'kategori'    => $kategori,
                'telepon'     => $telepon,
                'blok'        => $blok,
                'nomor_rumah' => $nomor,
                'alamat'      => $alamat,
                'setoran'     => $setoran,
                'berhenti'    => $opsi['berhenti'] ?? false,
                'alasan'      => $opsi['alasan'] ?? null,
                'hari_daftar' => $opsi['hari_daftar'] ?? (78 - ($urut % 8)),
            ];
        };

        // BLACKPINK
        $tambah('BLACKPINK', 'Jisoo Anggraini',   'aktif', 'langsung', 'warga', 12);
        $tambah('BLACKPINK', 'Jennie Wulandari',  'aktif', 'langsung', 'warga', 9);
        $tambah('BLACKPINK', 'Rose Pramesti',     'aktif', 'mandiri',  'warga', 7);
        $tambah('BLACKPINK', 'Lisa Kusuma',       'aktif', 'mandiri',  'warga', 10);

        // BTS
        $tambah('BTS', 'Namjoon Santoso',    'aktif',    'mandiri', 'warga', 6);
        $tambah('BTS', 'Seokjin Hartono',    'nonaktif', 'mandiri', 'warga', 5, ['berhenti' => true]);   // saldo dikosongkan -> uji anonimisasi
        $tambah('BTS', 'Yoongi Pratama',     'nonaktif', 'mandiri', 'warga', 4, ['berhenti' => true]);   // saldo tersisa -> anonimisasi ditolak
        $tambah('BTS', 'Hoseok Wijaya',      'aktif',    'mandiri', 'warga', 8);
        $tambah('BTS', 'Jimin Saputra',      'aktif',    'mandiri', 'warga', 11);
        $tambah('BTS', 'Taehyung Nugroho',   'aktif',    'mandiri', 'warga', 7);
        $tambah('BTS', 'Jungkook Firmansyah', 'aktif',   'mandiri', 'warga', 13);

        // NewJeans
        $tambah('NewJeans', 'Minji Lestari',    'aktif',    'mandiri', 'warga', 6);
        $tambah('NewJeans', 'Hanni Permata',    'aktif',    'mandiri', 'warga', 9);
        $tambah('NewJeans', 'Danielle Maharani', 'aktif',   'mandiri', 'warga', 5);
        $tambah('NewJeans', 'Haerin Kartika',   'aktif',    'mandiri', 'warga', 4);
        $tambah('NewJeans', 'Hyein Safitri',    'menunggu', 'mandiri', 'warga', 0, ['hari_daftar' => 2]);

        // aespa
        $tambah('aespa', 'Karina Dewanti',     'aktif',    'mandiri', 'warga', 8);
        $tambah('aespa', 'Giselle Rahmawati',  'aktif',    'mandiri', 'warga', 6);
        $tambah('aespa', 'Winter Purnama',     'aktif',    'mandiri', 'warga', 7);
        $tambah('aespa', 'Ningning Susanti',   'menunggu', 'mandiri', 'warga', 0, ['hari_daftar' => 1]);

        // Nasabah umum (bukan warga Taman Gading): alamat bebas
        $tambah('StrayKids', 'Felix Gunawan',     'aktif',    'mandiri', 'umum', 3, ['alamat' => 'Jl. Gajah Mada No. 17, Kaliwates, Jember']);
        $tambah('StrayKids', 'Hyunjin Salim',     'ditolak',  'mandiri', 'umum', 0, [
            'alamat' => 'Jl. Kalimantan No. 4, Sumbersari, Jember',
            'alasan' => 'Nomor telepon tidak aktif saat dikonfirmasi pengurus. Silakan daftar ulang dengan nomor yang benar.',
            'hari_daftar' => 12,
        ]);
        $tambah('TWICE', 'Sana Aprilia',          'menunggu', 'mandiri', 'umum', 0, ['alamat' => 'Jl. Mastrip Gg. 3 No. 21, Sumbersari, Jember', 'hari_daftar' => 3]);
        $tambah('TWICE', 'Chaeyoung Ramadhani',   'menunggu', 'mandiri', 'umum', 0, ['alamat' => 'Perumahan Tegal Besar Indah B-9, Kaliwates, Jember', 'hari_daftar' => 0]);

        return $out;
    }

    /** Jenis sampah tambahan khusus demo (agar keranjang timbangan bisa diuji dengan >= 5 baris). */
    public const JENIS_TAMBAHAN = [
        // nama => [aktif, [hariLalu => harga per kg]]
        'Kardus Album'     => [true,  [80 => 1700, 45 => 1800, 18 => 1900, 0 => 2000]],
        'Kertas Poster'    => [true,  [80 => 900,  45 => 1000, 18 => 1000, 0 => 1100]],
        'Botol Kaca'       => [true,  [80 => 400,  45 => 500,  18 => 450,  0 => 500]],
        'Kaleng Aluminium' => [true,  [80 => 9000, 45 => 9500, 18 => 10000, 0 => 9800]],
        'Styrofoam'        => [false, [80 => 300,  0 => 300]],   // sengaja nonaktif: uji penolakan jenis nonaktif
    ];

    /** Riwayat harga jenis bawaan (harga hari ini berasal dari HargaSampahSeeder). */
    public const RIWAYAT_JENIS_BAWAAN = [
        'Kertas'        => [80 => 1200, 45 => 1300, 18 => 1400],   // naik terus -> 1.500 hari ini
        'Botol Plastik' => [80 => 2800, 45 => 3200, 18 => 3100],   // turun ke 3.000 hari ini
        'Logam'         => [80 => 4500, 45 => 4800, 18 => 5200],   // turun ke 5.000 hari ini
    ];

    /** Kisaran berat per setoran (kg) tiap jenis, kelipatan 0,25 kg seperti tombol di timbangan. */
    public const KISARAN_BERAT = [
        'Kertas'           => [1.0, 6.0],
        'Botol Plastik'    => [0.5, 4.0],
        'Logam'            => [0.5, 8.0],
        'Kardus Album'     => [1.0, 7.0],
        'Kertas Poster'    => [0.25, 2.0],
        'Botol Kaca'       => [1.0, 5.0],
        'Kaleng Aluminium' => [0.25, 3.0],
    ];

    public const ALASAN_BATAL_SETORAN = [
        'Salah timbang: Kardus Album tercatat 15 kg, seharusnya 1,5 kg.',
        'Setoran tercatat atas nama yang salah (tertukar dengan tetangga sesama penggemar satu blok).',
        'Input ganda saat antrean ramai setelah acara nobar konser, satu catatan dihapus.',
        'Botol kaca pecah dan basah terkena air hujan, tidak memenuhi syarat sampah bersih.',
    ];
}
