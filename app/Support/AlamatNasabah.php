<?php

namespace App\Support;

/**
 * Aturan alamat nasabah (BR-19).
 * - warga: blok + nomor rumah di Perumahan Taman Gading (kolom alamat disusun otomatis).
 * - umum : alamat teks bebas.
 */
class AlamatNasabah
{
    public const PERUMAHAN = 'Perumahan Taman Gading';

    public const KATEGORI = ['warga', 'umum'];

    public static function rules(): array
    {
        return [
            'kategori' => ['required', 'in:warga,umum'],
            'blok' => ['nullable', 'required_if:kategori,warga', 'string', 'max:10', 'regex:/^[A-Za-z0-9\-]+$/'],
            'nomor_rumah' => ['nullable', 'required_if:kategori,warga', 'string', 'max:10', 'regex:/^[A-Za-z0-9\-\/]+$/'],
            'alamat' => ['nullable', 'required_if:kategori,umum', 'string', 'max:255'],
        ];
    }

    public static function pesan(): array
    {
        return [
            'kategori.required' => 'Pilih status domisili: warga Taman Gading atau umum.',
            'kategori.in' => 'Status domisili tidak valid.',
            'blok.required_if' => 'Blok wajib diisi untuk warga Taman Gading.',
            'blok.regex' => 'Blok hanya boleh berisi huruf, angka, dan tanda minus.',
            'nomor_rumah.required_if' => 'Nomor rumah wajib diisi untuk warga Taman Gading.',
            'nomor_rumah.regex' => 'Nomor rumah hanya boleh berisi huruf, angka, garis miring, dan tanda minus.',
            'alamat.required_if' => 'Alamat lengkap wajib diisi untuk nasabah umum.',
        ];
    }

    /**
     * Ubah masukan form menjadi kolom basis data: kategori, blok, nomor_rumah, alamat.
     */
    public static function normalisasi(array $data): array
    {
        if (($data['kategori'] ?? 'warga') === 'umum') {
            return [
                'kategori' => 'umum',
                'blok' => null,
                'nomor_rumah' => null,
                'alamat' => trim((string) ($data['alamat'] ?? '')),
            ];
        }

        $blok = strtoupper(trim((string) $data['blok']));
        $nomor = strtoupper(trim((string) $data['nomor_rumah']));

        return [
            'kategori' => 'warga',
            'blok' => $blok,
            'nomor_rumah' => $nomor,
            'alamat' => "Blok {$blok} No. {$nomor}, ".self::PERUMAHAN,
        ];
    }
}
