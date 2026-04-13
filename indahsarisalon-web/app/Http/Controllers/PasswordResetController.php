<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\SendOtpMail;

class PasswordResetController extends Controller
{
    // 1️⃣ Kirim OTP ke email
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $otp = rand(100000, 999999);

        PasswordReset::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10)
            ]
        );

        Mail::to($request->email)->send(new SendOtpMail($otp));

        // return response()->json(['message' => 'OTP terkirim ke email']);
         return redirect()->route('reset.password.otp', ['email' => $request->email])
                         ->with('success', 'OTP terkirim ke email');
    }

    // 2️⃣ Verifikasi OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $otpData = PasswordReset::where('email', $request->email)
                                ->where('otp', $request->otp)
                                ->first();

        if (!$otpData || $otpData->expires_at < Carbon::now()) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Kode OTP tidak valid atau sudah kedaluwarsa!');
        }

        return redirect()->route('reset.password.form', ['email' => $request->email, 'otp' => $request->otp])
                         ->with('success', 'OTP valid! Silakan buat kata sandi baru.');
    }

    // 3️⃣ Reset password
    public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'otp' => 'required',
        'password' => 'required|confirmed|min:8'
    ]);

    $otpData = PasswordReset::where('email', $request->email)
                            ->where('otp', $request->otp)
                            ->first();

    if (!$otpData || $otpData->expires_at < now()) {
        return redirect()->route('reset.password.otp', ['email' => $request->email])
                         ->with('error', 'OTP tidak valid atau sudah expired!');
    }

    // Update password user
    $user = User::where('email', $request->email)->first();
    $user->password = Hash::make($request->password);
    $user->save();

    $otpData->delete();

    return redirect()->route('auth')->with('success', 'Password berhasil di-reset');
}
}