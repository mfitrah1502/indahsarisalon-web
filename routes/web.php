<?php

use App\Http\Controllers\HolidayController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\CategoryController;

// ------------------------------
// Home & Landing
// ------------------------------
Route::get('/', [PageController::class, 'landing'])->name('landing');

// ------------------------------
// Form login & register
// ------------------------------
Route::get('/auth', fn() => view('auth'))->name('auth');
Route::get('/auth/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/auth/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/auth/register', [AuthController::class, 'register'])->name('register.process');

// Alternatif URL login/register
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ------------------------------
// Reset Password / OTP
// ------------------------------
Route::get('/reset-password', fn() => view('otp'))->name('reset.password');
Route::post('/reset-password', [PasswordResetController::class, 'sendOtp'])->name('reset.password.email');
Route::get('/reset-password/otp', fn(Request $request) => view('otp', ['email' => $request->email]))
    ->name('reset.password.otp');
Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('otp.verify');
Route::get('/new-password', fn(Request $request) => view('new-password', [
    'email' => $request->email,
    'otp' => $request->otp
]))->name('reset.password.form');
Route::post('/reset-password/otp', [PasswordResetController::class, 'resetPassword'])
    ->name('reset.password.update');

// ------------------------------
// Routes yang membutuhkan auth + session timeout
// ------------------------------
Route::middleware(['auth', 'session.timeout', 'prevent-back'])->group(function () {
    // ------------------------------
    // Profile
    // ------------------------------
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\ProfileController::class, 'index'])->name('index');
        Route::post('/update', [App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('update-info');
        Route::post('/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('change-password');
        Route::post('/avatar-update', [App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('avatar.update');
        Route::post('/otp-send', [App\Http\Controllers\ProfileController::class, 'sendOtp'])->name('otp.send');
        Route::post('/otp-verify', [App\Http\Controllers\ProfileController::class, 'verifyAndReset'])->name('otp.verify');
    });
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');

    // ------------------------------
    // Dashboard
    // ------------------------------
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/user', [PageController::class, 'dashboard'])->name('dashboard.user');

    // ------------------------------
    // Produk / Pelanggan
    // ------------------------------
    Route::prefix('pelanggan')->name('pelanggan.')->group(function () {
        Route::get('/', [PelangganController::class, 'index'])->name('index');
        Route::get('/create', [PelangganController::class, 'create'])->name('create');
        Route::post('/', [PelangganController::class, 'store'])->name('store');
        Route::get('/{pelanggan}/edit', [PelangganController::class, 'edit'])->name('edit');
        Route::put('/{pelanggan}', [PelangganController::class, 'update'])->name('update');
        Route::delete('/{pelanggan}', [PelangganController::class, 'destroy'])->name('destroy');
        Route::get('/{pelanggan}', [PelangganController::class, 'show'])->name('show');
    });

    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::resource('categories', CategoryController::class);

    // ------------------------------
    // Halaman About
    // ------------------------------
    Route::get('/about', [PageController::class, 'about'])->name('about');

    // ------------------------------
    // Management (Admin & Karyawan) - Pindahkan ke atas agar tidak bentrok dengan resource karyawan
    // ------------------------------
    Route::middleware('role:admin,karyawan')->group(function () {
        // Booking Management (Shared Logic)
        Route::get('/admin/bookings', [BookingController::class, 'adminIndex'])->name('admin.bookings.index');
        Route::get('/karyawan/bookings', [BookingController::class, 'adminIndex'])->name('karyawan.bookings.index');
        Route::get('/admin/bookings/{id}', [BookingController::class, 'show'])->name('admin.bookings.show');
        Route::patch('/admin/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('admin.bookings.updateStatus');
    });

    // ------------------------------
    // Karyawan
    // ------------------------------
    Route::get('karyawan/filter', [KaryawanController::class, 'filter'])->name('karyawan.filter');
    Route::resource('karyawan', KaryawanController::class);
    Route::get('/karyawan/{id}/absensi', [KaryawanController::class, 'absensi'])->name('karyawan.absensi');

    // ------------------------------
    // Absensi Kasir/Karyawan
    // ------------------------------
    Route::prefix('absensi')->middleware('role:admin,karyawan')->group(function () {
        Route::post('/masuk', [AbsensiController::class, 'absenMasuk'])->name('absensi.masuk');
        Route::post('/keluar', [AbsensiController::class, 'absenKeluar'])->name('absensi.keluar');
        
        // QR Attendance
        Route::get('/scan', [AbsensiController::class, 'showScanner'])->name('absensi.scan');
        Route::get('/konfirmasi', [AbsensiController::class, 'showConfirmation'])->name('absensi.confirmation');
        Route::post('/process-qr', [AbsensiController::class, 'processQR'])->name('absensi.processQR');
    });

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/absensi/qr', [AbsensiController::class, 'showQR'])->name('admin.absensi.qr');
        // Treatment
        Route::get('treatment/filter', [TreatmentController::class, 'filter'])->name('treatment.filter');
        Route::get('/treatment/filter-debug', [TreatmentController::class, 'filter'])->name('treatment.filter.debug');
        Route::resource('treatment', TreatmentController::class);

        // Pelanggan
        Route::get('pelanggan/filter', [PelangganController::class, 'filter'])->name('pelanggan.filter');
        Route::resource('pelanggan', PelangganController::class);

        // Hari Libur
        Route::resource('holidays', HolidayController::class)->only(['index', 'store', 'destroy']);

        // Keuangan (Not yet implemented)
        Route::get('/admin/keuangan/pemasukan', function () { abort(404); })->name('keuangan.pemasukan');
        Route::get('/admin/keuangan/pengeluaran', function () { abort(404); })->name('keuangan.pengeluaran');
    });



    //Booking
    // Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
    // Booking routes
// Route::prefix('booking')->name('booking.')->group(function () {
//     Route::get('/', [App\Http\Controllers\BookingController::class, 'index'])->name('index');

    //     // Route untuk pilih treatment -> stylist & jadwal
//     Route::get('/select/{treatment}', [App\Http\Controllers\BookingController::class, 'select'])
//         ->name('select');
// });
// // Route baru untuk summary
//     Route::post('/summary', [BookingController::class, 'summary'])->name('booking.summary');
    Route::middleware(['auth', 'prevent-back'])->group(function () {
        Route::get('/booking', [BookingController::class, 'index'])->name('booking.index'); // halaman daftar treatment
        Route::get('/booking/select/{treatmentId?}', [BookingController::class, 'select'])->name('booking.select'); // step 1
        Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store'); // simpan booking
        Route::post('/booking/check-stylist-availability', [BookingController::class, 'checkStylistAvailability'])->name('booking.check_stylist_availability');
        Route::get('/booking/summary/{bookingId}', [BookingController::class, 'summary'])->name('booking.summary'); // step summary
        Route::post('/booking/pay/{bookingId}', [BookingController::class, 'pay'])->name('booking.pay'); // bayar
        Route::get('/booking/history', [BookingController::class, 'history'])->name('booking.history'); // riwayat
        Route::post('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
        Route::post('/booking/{id}/update-payment-method', [BookingController::class, 'updatePaymentMethod'])->name('booking.updatePaymentMethod');
        Route::post('/booking/notification', [BookingController::class, 'handleNotification'])->name('booking.notification'); // webhook
    });
});

// Global fallback route inside web group (outside auth) to ensure session state for 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});