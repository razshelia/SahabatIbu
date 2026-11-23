<?php

namespace App\Services;

use App\Models\Nasabah;
use App\Models\User;
use App\Support\AlamatNasabah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class PendaftaranService
{
    /**
     * Generate nomor nasabah unik. Format: NSB-XXXX (contoh NSB-0001).
     */
    public static function generateNomorNasabah(): string
    {
        $lastId = Nasabah::max('id') ?? 0;
        $nextNumber = $lastId + 1;

        do {
            $nomor = sprintf('NSB-%04d', $nextNumber);
            $exists = Nasabah::where('nomor_nasabah', $nomor)->exists();
            if ($exists) {
                $nextNumber++;
            }
        } while ($exists);

        return $nomor;
    }

    /**
     * Registrasi mandiri nasabah baru (F-34, F-42).
     */
    public function registerMandiri(array $data): User
    {
        $alamat = AlamatNasabah::normalisasi($data);

        return DB::transaction(function () use ($data, $alamat) {
            $user = new User();
            $user->forceFill([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'], // casted as 'hashed'
                'role' => 'nasabah',
                'status' => 'menunggu',
                'email_verified_at' => now(), // otomatis verified di sistem lokal/prototipe atau lewat mail
            ]);
            $user->save();

            $nasabah = Nasabah::create([
                'user_id' => $user->id,
                'nomor_nasabah' => self::generateNomorNasabah(),
                'telepon' => $data['telepon'],
                ...$alamat,
                'saldo' => 0,
                'saldo_ditahan' => 0,
            ]);

            LogAktivitasService::catat(
                aksi: 'registrasi_mandiri',
                objekTipe: 'User',
                objekId: $user->id,
                keterangan: [
                    'persetujuan_privasi' => true,
                    'nomor_nasabah' => $nasabah->nomor_nasabah,
                    'kategori' => $nasabah->kategori,
                ],
                user: $user
            );

            return $user;
        });
    }

    /**
     * Melengkapi profil untuk pendaftar Google OAuth (F-35, F-42).
     */
    public function lengkapiProfilGoogle(User $user, array $data): Nasabah
    {
        $alamat = AlamatNasabah::normalisasi($data);

        return DB::transaction(function () use ($user, $data, $alamat) {
            $nasabah = $user->nasabah;

            if (! $nasabah) {
                $nasabah = Nasabah::create([
                    'user_id' => $user->id,
                    'nomor_nasabah' => self::generateNomorNasabah(),
                    'telepon' => $data['telepon'],
                    ...$alamat,
                    'saldo' => 0,
                    'saldo_ditahan' => 0,
                ]);
            } else {
                $nasabah->update([
                    'telepon' => $data['telepon'],
                    ...$alamat,
                ]);
            }

            LogAktivitasService::catat(
                aksi: 'lengkapi_profil_google',
                objekTipe: 'Nasabah',
                objekId: $nasabah->id,
                keterangan: [
                    'persetujuan_privasi' => true,
                    'nomor_nasabah' => $nasabah->nomor_nasabah,
                ],
                user: $user
            );

            return $nasabah;
        });
    }

    /**
     * Admin menambah nasabah langsung (langsung Aktif, F-07).
     */
    public function tambahNasabahLangsung(array $data, User $admin): User
    {
        $alamat = AlamatNasabah::normalisasi($data);

        return DB::transaction(function () use ($data, $admin, $alamat) {
            $user = new User();
            $user->forceFill([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'] ?? 'Password123!',
                'role' => 'nasabah',
                'status' => 'aktif',
                'verified_by' => $admin->id,
                'verified_at' => now(),
                'email_verified_at' => now(),
            ]);
            $user->save();

            $nasabah = Nasabah::create([
                'user_id' => $user->id,
                'nomor_nasabah' => self::generateNomorNasabah(),
                'telepon' => $data['telepon'],
                ...$alamat,
                'saldo' => 0,
                'saldo_ditahan' => 0,
            ]);

            LogAktivitasService::catat(
                aksi: 'admin_tambah_nasabah',
                objekTipe: 'User',
                objekId: $user->id,
                keterangan: [
                    'nomor_nasabah' => $nasabah->nomor_nasabah,
                    'dibuat_oleh' => $admin->email,
                ],
                user: $admin
            );

            return $user;
        });
    }

    /**
     * Admin memverifikasi pendaftar (F-38).
     */
    public function verifikasiPendaftar(User $user, bool $setujui, ?string $alasan, User $admin): void
    {
        if ($user->status !== 'menunggu') {
            throw new RuntimeException('Hanya pendaftar berstatus menunggu yang dapat diverifikasi.');
        }

        DB::transaction(function () use ($user, $setujui, $alasan, $admin) {
            if ($setujui) {
                $user->forceFill([
                    'status' => 'aktif',
                    'alasan_penolakan' => null,
                    'verified_by' => $admin->id,
                    'verified_at' => now(),
                ])->save();

                LogAktivitasService::catat(
                    aksi: 'verifikasi_nasabah_disetujui',
                    objekTipe: 'User',
                    objekId: $user->id,
                    keterangan: ['diverifikasi_oleh' => $admin->email],
                    user: $admin
                );
            } else {
                if (empty(trim($alasan ?? ''))) {
                    throw new RuntimeException('Alasan penolakan wajib diisi.');
                }

                $user->forceFill([
                    'status' => 'ditolak',
                    'alasan_penolakan' => $alasan,
                    'verified_by' => $admin->id,
                    'verified_at' => now(),
                ])->save();

                LogAktivitasService::catat(
                    aksi: 'verifikasi_nasabah_ditolak',
                    objekTipe: 'User',
                    objekId: $user->id,
                    keterangan: [
                        'alasan' => $alasan,
                        'diverifikasi_oleh' => $admin->email,
                    ],
                    user: $admin
                );
            }
        });
    }

    /**
     * Admin mengubah status aktif/nonaktif nasabah (F-10).
     */
    public function toggleStatusAktif(User $user, User $admin): void
    {
        if (! in_array($user->status, ['aktif', 'nonaktif'])) {
            throw new RuntimeException('Hanya akun aktif atau nonaktif yang dapat diubah statusnya.');
        }

        DB::transaction(function () use ($user, $admin) {
            $newStatus = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
            $user->forceFill(['status' => $newStatus])->save();

            LogAktivitasService::catat(
                aksi: $newStatus === 'aktif' ? 'aktifkan_nasabah' : 'nonaktifkan_nasabah',
                objekTipe: 'User',
                objekId: $user->id,
                keterangan: ['diubah_oleh' => $admin->email],
                user: $admin
            );
        });
    }

    /**
     * Admin menganonimkan data nasabah nonaktif (BR-18, F-43).
     */
    public function anonimisasi(Nasabah $nasabah, User $admin): void
    {
        $user = $nasabah->user;

        if ($user->status !== 'nonaktif') {
            throw new RuntimeException('Anonimisasi hanya dapat dilakukan pada akun dengan status Nonaktif.');
        }

        if (bccomp((string) $nasabah->saldo, '0', 2) !== 0 || bccomp((string) $nasabah->saldo_ditahan, '0', 2) !== 0) {
            throw new RuntimeException('Anonimisasi ditolak karena saldo nasabah belum nol.');
        }

        $adaPenarikanAktif = $nasabah->penarikanDana()
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->exists();

        if ($adaPenarikanAktif) {
            throw new RuntimeException('Anonimisasi ditolak karena masih terdapat penarikan berstatus Menunggu atau Disetujui.');
        }

        DB::transaction(function () use ($user, $nasabah, $admin) {
            $anonName = 'Anonim-' . $nasabah->id;
            $anonEmail = 'anonim' . $nasabah->id . '@banksampah.local';

            $user->forceFill([
                'name' => $anonName,
                'email' => $anonEmail,
                'password' => Hash::make(Str::random(32)),
                'google_id' => null,
            ])->save();

            $nasabah->update([
                'telepon' => '-',
                'alamat' => '-',
                'blok' => null,
                'nomor_rumah' => null,
            ]);

            // Kosongkan IP address dan User Agent pada log aktivitas akun ini untuk privasi (BR-18)
            $user->logAktivitas()->update([
                'ip_address' => null,
                'user_agent' => null,
            ]);

            LogAktivitasService::catat(
                aksi: 'data_dianonimkan',
                objekTipe: 'User',
                objekId: $user->id,
                keterangan: [
                    'nasabah_id' => $nasabah->id,
                    'nomor_nasabah' => $nasabah->nomor_nasabah,
                    'dianonimkan_oleh' => $admin->email,
                ],
                user: $admin
            );
        });
    }
}
