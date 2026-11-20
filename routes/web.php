<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminKatalogController;
use App\Http\Controllers\Admin\AdminLaporanController;
use App\Http\Controllers\Admin\AdminNasabahController;
use App\Http\Controllers\Admin\AdminPenarikanController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminSetoranController;
use App\Http\Controllers\Auth\AccountStatusController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ProfileCompletionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Nasabah\NasabahDashboardController;
use App\Http\Controllers\Nasabah\NasabahKatalogController;
use App\Http\Controllers\Nasabah\NasabahPenarikanController;
use App\Http\Controllers\Nasabah\NasabahProfileController;
use App\Http\Controllers\Nasabah\NasabahSetoranController;
use App\Http\Controllers\PrivacyPolicyController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Halaman Publik
Route::get('/kebijakan-privasi', [PrivacyPolicyController::class, 'show'])->name('privacy.policy');

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->must_change_password) {
            return redirect()->route('password.change.form');
        }
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if (! $user->nasabah || empty($user->nasabah->telepon) || empty($user->nasabah->alamat)) {
            return redirect()->route('profile.complete.form');
        }
        if ($user->status === 'menunggu' || $user->status === 'ditolak') {
            return redirect()->route('account.status');
        }
        return redirect()->route('nasabah.dashboard');
    }
    return redirect()->route('login');
});

// Area Tamu (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/lupa-kata-sandi', [PasswordController::class, 'showForgotForm'])->name('password.forgot.form');
    Route::post('/lupa-kata-sandi', [PasswordController::class, 'sendResetLink'])->name('password.forgot.send');
    Route::get('/reset-kata-sandi/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/reset-kata-sandi', [PasswordController::class, 'resetPassword'])->name('password.reset.update');

    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Area Autentikasi (Auth)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Status akun pendaftar (menunggu / ditolak)
    Route::get('/status-pendaftaran', [AccountStatusController::class, 'show'])->name('account.status');

    // Pelengkapan data kontak untuk akun Google
    Route::get('/lengkapi-profil', [ProfileCompletionController::class, 'show'])->name('profile.complete.form');
    Route::post('/lengkapi-profil', [ProfileCompletionController::class, 'store'])->name('profile.complete.store');

    // Ganti kata sandi (self-change atau wajib ganti pertama kali)
    Route::get('/ganti-kata-sandi', [PasswordController::class, 'showChangeForm'])->name('password.change.form');
    Route::post('/ganti-kata-sandi', [PasswordController::class, 'updatePassword'])->name('password.change.update');

    // Area Khusus Admin
    Route::prefix('admin')->name('admin.')->middleware(['role:admin', 'password.changed'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Nasabah & Pendaftar
        Route::get('/nasabah', [AdminNasabahController::class, 'index'])->name('nasabah.index');
        Route::get('/pendaftar', [AdminNasabahController::class, 'pendaftar'])->name('nasabah.pendaftar');
        Route::post('/pendaftar/{user}/verifikasi', [AdminNasabahController::class, 'verifikasi'])->name('nasabah.verifikasi');
        Route::get('/nasabah/tambah', [AdminNasabahController::class, 'create'])->name('nasabah.create');
        Route::post('/nasabah', [AdminNasabahController::class, 'store'])->name('nasabah.store');
        Route::get('/nasabah/{nasabah}/edit', [AdminNasabahController::class, 'edit'])->name('nasabah.edit');
        Route::put('/nasabah/{nasabah}', [AdminNasabahController::class, 'update'])->name('nasabah.update');
        Route::post('/nasabah/{user}/toggle-status', [AdminNasabahController::class, 'toggleStatus'])->name('nasabah.toggle-status');
        Route::post('/nasabah/{nasabah}/anonimkan', [AdminNasabahController::class, 'anonimkan'])->name('nasabah.anonimkan');

        // Katalog Jenis & Harga Sampah
        Route::get('/katalog', [AdminKatalogController::class, 'index'])->name('katalog.index');
        Route::post('/katalog/jenis', [AdminKatalogController::class, 'storeJenis'])->name('katalog.jenis.store');
        Route::put('/katalog/jenis/{jenis}', [AdminKatalogController::class, 'updateJenis'])->name('katalog.jenis.update');
        Route::post('/katalog/harga', [AdminKatalogController::class, 'storeHarga'])->name('katalog.harga.store');

        // Transaksi Setoran
        Route::get('/setoran', [AdminSetoranController::class, 'index'])->name('setoran.index');
        Route::get('/setoran/tambah', [AdminSetoranController::class, 'create'])->name('setoran.create');
        Route::post('/setoran', [AdminSetoranController::class, 'store'])->name('setoran.store');
        Route::get('/setoran/{setoran}', [AdminSetoranController::class, 'show'])->name('setoran.show');
        Route::post('/setoran/{setoran}/batalkan', [AdminSetoranController::class, 'batalkan'])->name('setoran.batalkan');

        // Penarikan Dana
        Route::get('/penarikan', [AdminPenarikanController::class, 'index'])->name('penarikan.index');
        Route::post('/penarikan/{penarikan}/setujui', [AdminPenarikanController::class, 'setujui'])->name('penarikan.setujui');
        Route::post('/penarikan/{penarikan}/tolak', [AdminPenarikanController::class, 'tolak'])->name('penarikan.tolak');
        Route::post('/penarikan/{penarikan}/cairkan', [AdminPenarikanController::class, 'cairkan'])->name('penarikan.cairkan');

        // Laporan & Ekspor
        Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-setoran', [AdminLaporanController::class, 'exportSetoran'])->name('laporan.export.setoran');
        Route::get('/laporan/export-penarikan', [AdminLaporanController::class, 'exportPenarikan'])->name('laporan.export.penarikan');
        Route::get('/laporan/cetak', [AdminLaporanController::class, 'cetak'])->name('laporan.cetak');

        // Profil & Log Aktivitas
        Route::get('/profil', [AdminProfileController::class, 'show'])->name('profile.show');
    });

    // Area Khusus Nasabah
    Route::prefix('nasabah')->name('nasabah.')->middleware(['role:nasabah', 'account.active', 'profile.complete'])->group(function () {
        Route::get('/', [NasabahDashboardController::class, 'index'])->name('dashboard');

        // Riwayat Setoran
        Route::get('/setoran', [NasabahSetoranController::class, 'index'])->name('setoran.index');
        Route::get('/setoran/{setoran}', [NasabahSetoranController::class, 'show'])->name('setoran.show');

        // Pengajuan & Riwayat Penarikan Dana
        Route::get('/penarikan', [NasabahPenarikanController::class, 'index'])->name('penarikan.index');
        Route::post('/penarikan', [NasabahPenarikanController::class, 'store'])->name('penarikan.store');
        Route::post('/penarikan/{penarikan}/batalkan', [NasabahPenarikanController::class, 'batalkan'])->name('penarikan.batalkan');

        // Katalog Harga Berlaku
        Route::get('/katalog', [NasabahKatalogController::class, 'index'])->name('katalog.index');

        // Profil Nasabah
        Route::get('/profil', [NasabahProfileController::class, 'show'])->name('profile.show');
        Route::put('/profil', [NasabahProfileController::class, 'update'])->name('profile.update');
    });
});
