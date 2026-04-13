<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
{
    $timeout = 50000; // detik

    if (Auth::check()) {
        $currentTime = time();

        // Ambil last activity (kalau belum ada, set pertama kali)
        if (!session()->has('lastActivityTime')) {
            session(['lastActivityTime' => $currentTime]);
        }

        $lastActivity = session('lastActivityTime');

        // Cek apakah sudah lewat timeout
        if (($currentTime - $lastActivity) > $timeout) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('auth')
                ->with('error', 'Session habis karena tidak ada aktivitas.');
        }

        // Update waktu aktivitas SETELAH pengecekan
        session(['lastActivityTime' => $currentTime]);
    }

    return $next($request);
}
}
