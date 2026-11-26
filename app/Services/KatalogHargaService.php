<?php

namespace App\Services;

use App\Models\HargaSampah;
use App\Models\JenisSampah;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class KatalogHargaService
{
    /**
     * Dapatkan semua jenis sampah beserta harga yang berlaku saat ini (F-15).
     */
    public function getKatalogBerlaku(): Collection
    {
        $jenisList = JenisSampah::where('aktif', true)->get();

        foreach ($jenisList as $jenis) {
            $jenis->setRelation('harga_berlaku', $jenis->hargaBerlaku());
        }

        return $jenisList;
    }

    /**
     * Tambah atau perbarui jenis sampah (F-12).
     */
    public function simpanJenisSampah(array $data, ?JenisSampah $jenis = null, ?User $admin = null): JenisSampah
    {
        return DB::transaction(function () use ($data, $jenis, $admin) {
            if ($jenis) {
                $jenis->update([
                    'nama' => $data['nama'],
                    'satuan' => $data['satuan'] ?? 'kg',
                    'aktif' => isset($data['aktif']) ? (bool) $data['aktif'] : $jenis->aktif,
                ]);

                if ($admin) {
                    LogAktivitasService::catat(
                        aksi: 'ubah_jenis_sampah',
                        objekTipe: 'JenisSampah',
                        objekId: $jenis->id,
                        keterangan: ['nama' => $jenis->nama, 'aktif' => $jenis->aktif],
                        user: $admin
                    );
                }
            } else {
                $jenis = JenisSampah::create([
                    'nama' => $data['nama'],
                    'satuan' => $data['satuan'] ?? 'kg',
                    'aktif' => true,
                ]);

                if ($admin) {
                    LogAktivitasService::catat(
                        aksi: 'tambah_jenis_sampah',
                        objekTipe: 'JenisSampah',
                        objekId: $jenis->id,
                        keterangan: ['nama' => $jenis->nama],
                        user: $admin
                    );
                }
            }

            return $jenis;
        });
    }

    /**
     * Simpan harga jenis sampah (F-13, F-14).
     * Harga untuk jenis dan tanggal yang sama diperbarui agar sesuai batas unik tabel.
     */
    public function tambahHarga(int $jenisSampahId, float $hargaPerKg, string $berlakuMulai, User $admin): HargaSampah
    {
        if ($hargaPerKg <= 0) {
            throw new RuntimeException('Harga per satuan harus lebih dari 0.');
        }

        $jenis = JenisSampah::findOrFail($jenisSampahId);

        return DB::transaction(function () use ($jenis, $hargaPerKg, $berlakuMulai, $admin) {
            $harga = HargaSampah::query()
                ->where('jenis_sampah_id', $jenis->id)
                ->whereDate('berlaku_mulai', $berlakuMulai)
                ->first() ?? new HargaSampah([
                    'jenis_sampah_id' => $jenis->id,
                    'berlaku_mulai' => $berlakuMulai,
                ]);

            $hargaBaru = ! $harga->exists;
            $hargaSebelumnya = $harga->harga_per_kg;
            $harga->harga_per_kg = number_format($hargaPerKg, 2, '.', '');

            if ($hargaBaru) {
                $harga->dibuat_oleh = $admin->id;
            }

            $harga->save();

            $keterangan = [
                'jenis_sampah' => $jenis->nama,
                'harga_per_kg' => $harga->harga_per_kg,
                'berlaku_mulai' => $berlakuMulai,
            ];

            if ($hargaBaru) {
                $keterangan['dibuat_oleh'] = $admin->email;
            } else {
                $keterangan['harga_sebelumnya'] = $hargaSebelumnya;
                $keterangan['diubah_oleh'] = $admin->email;
            }

            LogAktivitasService::catat(
                aksi: $hargaBaru ? 'tambah_harga_sampah' : 'ubah_harga_sampah',
                objekTipe: 'HargaSampah',
                objekId: $harga->id,
                keterangan: $keterangan,
                user: $admin
            );

            return $harga;
        });
    }
}
