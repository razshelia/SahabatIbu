<?php

namespace Tests\Feature;

use App\Models\HargaSampah;
use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Models\PenarikanDana;
use App\Models\TransaksiSetoran;
use App\Models\User;
use App\Services\PendaftaranService;
use Database\Seeders\AdminSeeder;
use Database\Seeders\DemoSeeder;
use Database\Seeders\HargaSampahSeeder;
use Database\Seeders\JenisSampahSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'bank_sampah.admin.nama' => 'Admin Uji',
            'bank_sampah.admin.email' => 'admin@example.test',
            'bank_sampah.admin.password' => 'Admin12345',
            'bank_sampah.admin.reset_password' => false,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();   // seeder demo memakai tanggal mundur; pastikan selalu dikembalikan
        parent::tearDown();
    }

    // ---------------------------------------------------------------- AdminSeeder

    public function test_admin_seeder_membuat_admin_dengan_kata_sandi_ter_hash_dan_wajib_ganti(): void
    {
        $this->seed(AdminSeeder::class);

        $admin = User::where('email', 'admin@example.test')->firstOrFail();
        $this->assertSame('admin', $admin->role);
        $this->assertSame('aktif', $admin->status);
        $this->assertTrue($admin->must_change_password);
        $this->assertNotSame('Admin12345', $admin->password);
        $this->assertTrue(Hash::check('Admin12345', $admin->password));
    }

    public function test_admin_seeder_dijalankan_ulang_tidak_menimpa_kata_sandi_yang_sudah_diganti(): void
    {
        $this->seed(AdminSeeder::class);

        User::where('role', 'admin')->firstOrFail()->forceFill([
            'password' => 'SudahDiganti99',
            'must_change_password' => false,
        ])->save();

        $this->seed(AdminSeeder::class);

        $admin = User::where('role', 'admin')->firstOrFail();
        $this->assertTrue(Hash::check('SudahDiganti99', $admin->password));
        $this->assertFalse($admin->must_change_password);
        $this->assertSame(1, User::where('role', 'admin')->count());
    }

    public function test_admin_seeder_boleh_mereset_kata_sandi_hanya_jika_diminta_eksplisit(): void
    {
        $this->seed(AdminSeeder::class);
        User::where('role', 'admin')->firstOrFail()->forceFill(['password' => 'SudahDiganti99', 'must_change_password' => false])->save();

        config(['bank_sampah.admin.reset_password' => true]);
        $this->seed(AdminSeeder::class);

        $admin = User::where('role', 'admin')->firstOrFail();
        $this->assertTrue(Hash::check('Admin12345', $admin->password));
        $this->assertTrue($admin->must_change_password);
    }

    public function test_admin_seeder_menolak_email_milik_akun_nasabah(): void
    {
        $u = new User();
        $u->forceFill(['name' => 'Nasabah', 'email' => 'admin@example.test', 'password' => 'Password123', 'role' => 'nasabah', 'status' => 'aktif'])->save();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('sudah dipakai akun nasabah');

        try {
            $this->seed(AdminSeeder::class);
        } finally {
            $this->assertSame('nasabah', $u->fresh()->role, 'akun nasabah tidak boleh berubah jadi admin');
        }
    }

    public function test_admin_seeder_menolak_pengaturan_kosong_atau_kata_sandi_lemah(): void
    {
        foreach ([
            ['bank_sampah.admin.email', ''],
            ['bank_sampah.admin.password', ''],
            ['bank_sampah.admin.password', 'pendek1'],
            ['bank_sampah.admin.password', 'hanyahuruf'],
            ['bank_sampah.admin.password', '1234567890'],
            ['bank_sampah.admin.email', 'bukan-email'],
        ] as [$kunci, $nilai]) {
            config([
                'bank_sampah.admin.email' => 'admin@example.test',
                'bank_sampah.admin.password' => 'Admin12345',
                $kunci => $nilai,
            ]);

            try {
                $this->seed(AdminSeeder::class);
                $this->fail("Seeder seharusnya menolak {$kunci} = \"{$nilai}\"");
            } catch (RuntimeException) {
                $this->assertSame(0, User::where('role', 'admin')->count());
            }
        }
    }

    // ---------------------------------------------------------------- HargaSampahSeeder

    public function test_harga_seeder_tidak_menimpa_harga_yang_sudah_ditetapkan_pengurus(): void
    {
        $this->seed(AdminSeeder::class);
        $this->seed(JenisSampahSeeder::class);
        $admin = User::where('role', 'admin')->firstOrFail();
        $kertas = JenisSampah::where('nama', 'Kertas')->firstOrFail();

        // Pengurus sudah menetapkan harga Kertas sejak beberapa hari lalu.
        HargaSampah::create([
            'jenis_sampah_id' => $kertas->id,
            'harga_per_kg' => 2000,
            'berlaku_mulai' => now()->subDays(5)->toDateString(),
            'dibuat_oleh' => $admin->id,
        ]);

        $this->seed(HargaSampahSeeder::class);

        $this->assertEquals(2000, $kertas->hargaBerlaku()->harga_per_kg, 'harga contoh tidak boleh menimpa harga asli');
        $this->assertSame(1, HargaSampah::where('jenis_sampah_id', $kertas->id)->count());
        // Jenis yang belum punya harga tetap diberi harga awal.
        $this->assertNotNull(JenisSampah::where('nama', 'Logam')->firstOrFail()->hargaBerlaku());

        // Dijalankan lagi (mis. di hari berikutnya) tidak menambah baris apa pun.
        $jumlah = HargaSampah::count();
        Carbon::setTestNow(now()->addDays(3));
        $this->seed(HargaSampahSeeder::class);
        $this->assertSame($jumlah, HargaSampah::count());
    }

    // ---------------------------------------------------------------- Seeder demo

    public function test_seeder_demo_menolak_berjalan_di_production(): void
    {
        $this->seed(AdminSeeder::class);
        $this->app->detectEnvironment(fn () => 'production');

        try {
            // Dipanggil langsung: lewat `db:seed` di production Laravel lebih dulu meminta konfirmasi (--force).
            $this->app->make(DemoSeeder::class)->run();
            $this->fail('DemoSeeder seharusnya ditolak di production');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('production', $e->getMessage());
        }

        $this->assertSame(0, Nasabah::count());
    }

    public function test_seeder_demo_membuat_data_k_pop_yang_lengkap_dan_konsisten(): void
    {
        $this->seed(DemoSeeder::class);

        // Cukup banyak dan mencakup semua keadaan yang perlu diuji.
        $this->assertGreaterThanOrEqual(20, Nasabah::count());
        $this->assertGreaterThanOrEqual(100, TransaksiSetoran::count());
        foreach (['menunggu', 'ditolak', 'aktif', 'nonaktif'] as $status) {
            $this->assertGreaterThan(0, User::where('role', 'nasabah')->where('status', $status)->count(), "tidak ada nasabah {$status}");
        }
        foreach (['menunggu', 'disetujui', 'dicairkan', 'ditolak', 'dibatalkan'] as $status) {
            $this->assertGreaterThan(0, PenarikanDana::where('status', $status)->count(), "tidak ada penarikan {$status}");
        }
        $this->assertGreaterThan(0, PenarikanDana::where('metode', 'transfer')->count());
        $this->assertGreaterThan(0, TransaksiSetoran::where('status', 'dibatalkan')->count());
        $this->assertGreaterThan(0, Nasabah::where('kategori', 'umum')->count());
        $this->assertGreaterThanOrEqual(7, JenisSampah::where('aktif', true)->count(), 'keranjang timbangan perlu >= 5 jenis');

        // Bertema K-pop.
        $this->assertTrue(User::where('email', 'jungkook.bts@example.test')->exists());
        $this->assertTrue(User::where('email', 'jisoo.blackpink@example.test')->exists());

        // Saldo mengikuti aturan bisnis: setoran berhasil - penarikan dicairkan; ditahan = menunggu + disetujui.
        foreach (Nasabah::all() as $n) {
            $masuk = (float) TransaksiSetoran::where('nasabah_id', $n->id)->where('status', 'berhasil')->sum('total_nilai');
            $cair = (float) PenarikanDana::where('nasabah_id', $n->id)->where('status', 'dicairkan')->sum('nominal');
            $tahan = (float) PenarikanDana::where('nasabah_id', $n->id)->whereIn('status', ['menunggu', 'disetujui'])->sum('nominal');

            $this->assertEqualsWithDelta($masuk - $cair, (float) $n->saldo, 0.005, "saldo {$n->nomor_nasabah}");
            $this->assertEqualsWithDelta($tahan, (float) $n->saldo_ditahan, 0.005, "saldo ditahan {$n->nomor_nasabah}");
            $this->assertLessThanOrEqual((float) $n->saldo + 0.005, (float) $n->saldo_ditahan);
        }

        // Harga pada tiap detail = harga yang berlaku PADA tanggal setoran (riwayat harga naik/turun).
        foreach (TransaksiSetoran::with('detail')->get() as $t) {
            foreach ($t->detail as $d) {
                $harga = HargaSampah::where('jenis_sampah_id', $d->jenis_sampah_id)
                    ->whereDate('berlaku_mulai', '<=', $t->tanggal->toDateString())
                    ->orderByDesc('berlaku_mulai')->firstOrFail();
                $this->assertEquals((float) $harga->harga_per_kg, (float) $d->harga_per_kg, "harga {$t->kode}");
            }
        }

        // Kasus khusus untuk menguji anonimisasi (BR-18): satu nonaktif saldo nol, satu nonaktif masih bersaldo.
        $seokjin = Nasabah::whereHas('user', fn ($q) => $q->where('email', 'seokjin.bts@example.test'))->firstOrFail();
        $yoongi = Nasabah::whereHas('user', fn ($q) => $q->where('email', 'yoongi.bts@example.test'))->firstOrFail();
        $this->assertSame('nonaktif', $seokjin->user->status);
        $this->assertEquals(0, (float) $seokjin->saldo);
        $this->assertSame('nonaktif', $yoongi->user->status);
        $this->assertGreaterThan(0, (float) $yoongi->saldo);

        // Tidak ada jenis nonaktif yang pernah disetor, dan tidak ada tanggal di masa depan.
        $this->assertSame(0, DB::table('detail_setoran')->join('jenis_sampah', 'jenis_sampah.id', '=', 'jenis_sampah_id')->where('aktif', false)->count());
        $this->assertSame(0, TransaksiSetoran::where('tanggal', '>', now())->count());
        $this->assertSame(0, PenarikanDana::where('diajukan_pada', '>', now())->count());
    }

    public function test_seeder_demo_aman_dijalankan_ulang_dan_tidak_menggandakan_data(): void
    {
        $this->seed(DemoSeeder::class);
        $hitung = fn () => [User::count(), Nasabah::count(), TransaksiSetoran::count(), PenarikanDana::count(), HargaSampah::count(), DB::table('log_aktivitas')->count()];
        $sebelum = $hitung();

        $this->seed(DemoSeeder::class);

        $this->assertSame($sebelum, $hitung());
    }

    public function test_seeder_demo_tidak_bentrok_dengan_nasabah_nyata_yang_sudah_ada(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('role', 'admin')->firstOrFail();

        // Nasabah asli sudah mengambil NSB-0001 lewat alur pendaftaran biasa.
        app(PendaftaranService::class)->tambahNasabahLangsung([
            'name' => 'Ibu Warga Asli', 'email' => 'asli@example.test', 'telepon' => '08123456789',
            'kategori' => 'warga', 'blok' => 'Z', 'nomor_rumah' => '99', 'password' => 'Password123',
        ], $admin);

        $this->seed(DemoSeeder::class);

        $this->assertSame('NSB-0001', Nasabah::whereHas('user', fn ($q) => $q->where('email', 'asli@example.test'))->firstOrFail()->nomor_nasabah);
        $this->assertSame(Nasabah::count(), Nasabah::distinct('nomor_nasabah')->count('nomor_nasabah'));
        $this->assertSame(0, User::where('role', 'nasabah')->doesntHave('nasabah')->count(), 'tidak boleh ada akun nasabah tanpa data nasabah');
    }

    public function test_halaman_utama_admin_dan_nasabah_bisa_dibuka_dengan_data_demo(): void
    {
        $this->seed(DemoSeeder::class);

        $admin = User::where('role', 'admin')->firstOrFail();
        $admin->forceFill(['must_change_password' => false])->save();

        foreach (['admin.dashboard', 'admin.nasabah.index', 'admin.nasabah.pendaftar', 'admin.setoran.index',
                  'admin.setoran.create', 'admin.penarikan.index', 'admin.katalog.index', 'admin.laporan.index'] as $rute) {
            $this->actingAs($admin)->get(route($rute))->assertOk();
        }
        $this->actingAs($admin)->get(route('admin.setoran.show', TransaksiSetoran::firstOrFail()))->assertOk();
        $this->actingAs($admin)->get(route('admin.penarikan.index', ['status' => 'menunggu']))->assertOk();

        $jungkook = User::where('email', 'jungkook.bts@example.test')->firstOrFail();
        foreach (['nasabah.dashboard', 'nasabah.setoran.index', 'nasabah.penarikan.index', 'nasabah.katalog.index', 'nasabah.profile.show'] as $rute) {
            $this->actingAs($jungkook)->get(route($rute))->assertOk();
        }
        $this->actingAs($jungkook)->get(route('garasi'))->assertOk();
    }
}
