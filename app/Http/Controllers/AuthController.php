<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth'); // resources/views/login.blade.php
    }

    // Memproses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            session()->put('show_promo_modal', true);
            
            $user = Auth::user();

            // Kirim WA Promo jika login sebagai pelanggan
            if (strtolower($user->role) === 'pelanggan') {
                $promoTreatments = \App\Models\Treatment::where('is_promo', true)->get();
                if ($promoTreatments->count() > 0) {
                $promoDetails = $promoTreatments->map(function($t) {
                    $discount = ($t->promo_type == 'percentage') ? $t->promo_value . '%' : 'Rp ' . number_format($t->promo_value, 0, ',', '.');
                    return "- " . $t->name . " (DISKON " . $discount . ")";
                })->implode("\n");

                $message = "Halo {$user->name}, ada promo spesial di Indah Sari Salon!\n\nTreatment promo hari ini:\n{$promoDetails}\n\nBooking sekarang sebelum kehabisan slot: " . route('dashboard') . "\n\nSampai jumpa di salon!";
                
                \App\Services\WhatsAppService::sendMessage($user->phone, $message);
            }    }

            if ($user->role == 'admin' || $user->role == 'karyawan') {
                return redirect()->intended(route('dashboard')); // Redirect ke tujuan awal atau dashboard
            } else {
                return redirect()->route('dashboard.user'); // dashboard pelanggan tetap ke dashboard user
            }
        }

        return back()->with('error', 'Username atau password salah!');
    }

    // Menampilkan form register
    public function showRegisterForm()
    {
        session()->now('showRegister', true);
        return view('auth');
    }

    // Memproses register
    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|unique:users',
        'phone' => 'required|string|max:15',
        'username' => 'required|string|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'email_verified_at' => now(),
    'remember_token' => Str::random(60),
    ]);

    $user = User::create([
        'name' => $request->name,                   // wajib
        'email' => $request->email,
        'phone' => $request->phone,
        'username' => $request->username,
        'password' => Hash::make($request->password),
        'role' => $request->role ?? 'pelanggan',
        'email_verified_at' => now(),
        'remember_token' => Str::random(60),
    ]);

    return redirect()->route('auth')->with('success', 'Akun berhasil dibuat. Silahkan login!');
}

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('auth');
    }
}