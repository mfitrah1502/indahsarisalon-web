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
use Illuminate\Support\Str;

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
    
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\ProfileController::class, 'index'])->name('index');
        Route::post('/update', [App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('update-info');
        Route::post('/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('change-password');
        Route::post('/avatar-update', [App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('avatar.update');
        Route::post('/otp-send', [App\Http\Controllers\ProfileController::class, 'sendOtp'])->name('otp.send');
        Route::post('/otp-verify', [App\Http\Controllers\ProfileController::class, 'verifyAndReset'])->name('otp.verify');
    });
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');

    // Dashboard
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/user', [PageController::class, 'dashboard'])->name('dashboard.user');

    // Pelanggan (Umum untuk auth)
    Route::prefix('pelanggan')->name('pelanggan.')->group(function () {
        Route::get('/', [PelangganController::class, 'index'])->name('index');
        Route::get('/create', [PelangganController::class, 'create'])->name('create');
        Route::post('/', [PelangganController::class, 'store'])->name('store');
        Route::get('/{pelanggan}/edit', [PelangganController::class, 'edit'])->name('edit');
        Route::put('/{pelanggan}', [PelangganController::class, 'update'])->name('update');
        Route::delete('/{pelanggan}', [PelangganController::class, 'destroy'])->name('destroy');
        Route::get('/{pelanggan}', [PelangganController::class, 'show'])->name('show');
        Route::get('/{id}/history', [PelangganController::class, 'history'])->name('history');
    });

    Route::resource('categories', CategoryController::class);
    Route::get('/about', [PageController::class, 'about'])->name('about');

    // ------------------------------
    // Area Owner & Admin (Akses Manajemen)
    // ------------------------------
    Route::middleware('role:owner,admin')->group(function () {
        // Treatment Management
        Route::get('/admin/treatment/details/{id}', [App\Http\Controllers\TreatmentController::class, 'getDetails'])->name('treatment.get-details');
        
        // Booking Management
        Route::get('/admin/bookings', [BookingController::class, 'adminIndex'])->name('admin.bookings.index');
        Route::get('/admin/bookings/{id}', [BookingController::class, 'show'])->name('admin.bookings.show');
        Route::patch('/admin/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('admin.bookings.updateStatus');
        Route::patch('/admin/bookings/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('admin.bookings.reschedule');
        Route::get('/admin/bookings/{id}/print', [BookingController::class, 'printReceipt'])->name('admin.bookings.print');

        // Treatment Management
        Route::get('treatment/filter', [TreatmentController::class, 'filter'])->name('treatment.filter');
        Route::get('/treatment/filter-debug', [TreatmentController::class, 'filter'])->name('treatment.filter.debug');
        Route::get('/treatment/broadcast-promo', [TreatmentController::class, 'broadcastPromo'])->name('treatment.broadcast');
        Route::resource('treatment', TreatmentController::class);

        // Karyawan & Pelanggan Filter
        Route::get('karyawan/filter', [KaryawanController::class, 'filter'])->name('karyawan.filter');
        Route::resource('karyawan', KaryawanController::class);
        Route::get('/karyawan/{id}/absensi', [KaryawanController::class, 'absensi'])->name('karyawan.absensi');
        Route::get('pelanggan/filter', [PelangganController::class, 'filter'])->name('pelanggan.filter');

        // Hari Libur
        Route::resource('holidays', HolidayController::class)->only(['index', 'store', 'destroy']);
    });

    // ------------------------------
    // Absensi (Owner, Admin, Karyawan)
    // ------------------------------
    Route::prefix('absensi')->middleware('role:owner,admin,karyawan')->group(function () {
        Route::post('/presence', [AbsensiController::class, 'presence'])->name('absensi.presence');
        Route::get('/scan', [AbsensiController::class, 'showScanner'])->name('absensi.scan');
        Route::get('/konfirmasi', [AbsensiController::class, 'showConfirmation'])->name('absensi.confirmation');
        Route::post('/process-qr', [AbsensiController::class, 'processQR'])->name('absensi.processQR');
    });

    // ------------------------------
    // Khusus Owner (Keuangan & QR Master)
    // ------------------------------
    Route::middleware('role:owner')->group(function () {
        Route::get('/admin/absensi/qr', [AbsensiController::class, 'showQR'])->name('admin.absensi.qr');
        Route::post('/admin/absensi/manual', [AbsensiController::class, 'storeManual'])->name('absensi.storeManual');
        
        Route::get('/admin/keuangan/pemasukan', [App\Http\Controllers\KeuanganController::class, 'pemasukan'])->name('keuangan.pemasukan');
        Route::get('/admin/keuangan/pengeluaran', [App\Http\Controllers\KeuanganController::class, 'pengeluaran'])->name('keuangan.pengeluaran');
        Route::post('/admin/keuangan/pengeluaran', [App\Http\Controllers\KeuanganController::class, 'storePengeluaran'])->name('keuangan.pengeluaran.store');
        Route::get('/admin/keuangan/profit/export', [App\Http\Controllers\KeuanganController::class, 'exportProfitPdf'])->name('keuangan.profit.export');
    });

    // ------------------------------
    // Booking Client
    // ------------------------------
    Route::middleware(['auth', 'prevent-back'])->group(function () {
        Route::get('/booking', [BookingController::class, 'index'])->name('booking.index'); 
        Route::get('/booking/select/{treatmentId?}', [BookingController::class, 'select'])->name('booking.select'); 
        Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store'); 
        Route::post('/booking/check-stylist-availability', [BookingController::class, 'checkStylistAvailability'])->name('booking.check_stylist_availability');
        Route::post('/booking/check-booked-stylists', [BookingController::class, 'checkBookedStylists'])->name('booking.check_booked_stylists');
        Route::get('/booking/summary/{bookingId}', [BookingController::class, 'summary'])->name('booking.summary'); 
        Route::post('/booking/pay/{bookingId}', [BookingController::class, 'pay'])->name('booking.pay'); 
        Route::get('/booking/history', [BookingController::class, 'history'])->name('booking.history'); 
        Route::post('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
        Route::post('/booking/{id}/update-payment-method', [BookingController::class, 'updatePaymentMethod'])->name('booking.updatePaymentMethod');
        Route::post('/booking/notification', [BookingController::class, 'handleNotification'])->name('booking.notification');
    });
});

// Fallback
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});