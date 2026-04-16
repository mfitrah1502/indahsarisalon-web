<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    // Tombol absen masuk manual
    public function absenMasuk(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // cek role karyawan langsung di controller
        if (!in_array($user->role, ['admin', 'karyawan'])) {
            abort(403, 'Unauthorized');
        }

        $today = now()->format('Y-m-d');

        // Cek apakah sudah absen hari ini
        $absen = Absensi::firstOrCreate(
            ['user_id' => $user->id, 'tanggal' => $today],
            ['jam_masuk' => now(), 'status' => 'Hadir']
        );

        return response()->json(['success' => true, 'message' => 'Absen masuk tercatat']);
    }

    // Tombol absen keluar manual
    public function absenKeluar(Request $request)
    {
        $user = Auth::user();

        // cek role
        if (!in_array($user->role, ['admin', 'karyawan'])) {
            abort(403, 'Unauthorized');
        }

        $today = now()->format('Y-m-d');

        $absen = Absensi::where('user_id', $user->id)
                        ->where('tanggal', $today)
                        ->first();

        if($absen){
            $absen->update(['jam_keluar' => now()]);
            return response()->json(['success' => true, 'message' => 'Absen keluar tercatat']);
        }

        return response()->json(['success' => false, 'message' => 'Belum absen masuk']);
    }

    public function showScanner()
    {
        return view('absensi.scan');
    }

    public function showConfirmation(Request $request)
    {
        $token = $request->query('token');
        $date = now()->format('Y-m-d');
        $expectedToken = md5($date . config('app.key'));

        if (!$token || $token !== $expectedToken) {
            abort(403, 'Link presensi tidak valid atau sudah kadaluarsa.');
        }

        $user = Auth::user();
        $absen = Absensi::where('user_id', $user->id)
                        ->where('tanggal', $date)
                        ->first();

        // Tentukan tipe presensi
        $type = 'Masuk';
        $alreadyDone = false;
        $message = '';

        if ($absen) {
            if ($absen->jam_keluar) {
                $alreadyDone = true;
                $message = 'Anda sudah melakukan absen masuk dan keluar hari ini.';
            } else {
                $type = 'Keluar';
            }
        }

        return view('absensi.confirm', compact('token', 'type', 'user', 'alreadyDone', 'message'));
    }

    public function showQR()
    {
        // Simple token: md5(date + APP_KEY)
        $date = now()->format('Y-m-d');
        $token = md5($date . config('app.key'));
        
        return view('absensi.qr', compact('token'));
    }

    public function processQR(Request $request)
    {
        $request->validate(['token' => 'required']);
        
        $today = now()->format('Y-m-d');
        $expectedToken = md5($today . config('app.key'));

        if ($request->token !== $expectedToken) {
            $msg = 'QR Code tidak valid atau sudah kadaluarsa.';
            if ($request->ajax()) return response()->json(['success' => false, 'message' => $msg]);
            return redirect()->back()->with('error', $msg);
        }

        $user = Auth::user();

        // Logic Absen Masuk atau Keluar
        $absen = Absensi::where('user_id', $user->id)
                        ->where('tanggal', $today)
                        ->first();

        if (!$absen) {
            // Belum absen hari ini -> Masuk
            Absensi::create([
                'user_id' => $user->id,
                'tanggal' => $today,
                'jam_masuk' => now(),
                'status' => 'Hadir'
            ]);
            $msg = 'Absen MASUK berhasil tercatat.';
            $type = 'masuk';
        } else {
            // Sudah absen masuk -> Keluar (jika belum absen keluar)
            if ($absen->jam_keluar) {
                $msg = 'Anda sudah melakukan absen masuk dan keluar hari ini.';
                if ($request->ajax()) return response()->json(['success' => false, 'message' => $msg]);
                return redirect()->back()->with('error', $msg);
            }
            $absen->update(['jam_keluar' => now()]);
            $msg = 'Absen KELUAR berhasil tercatat.';
            $type = 'keluar';
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg, 'type' => $type]);
        }

        return redirect()->back()->with('success', $msg);
    }
}
