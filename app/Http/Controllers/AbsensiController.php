<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    // Tombol presensi tunggal
    public function presence(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // cek role
        if (!in_array($user->role, ['owner', 'admin', 'karyawan'])) {
            abort(403, 'Unauthorized');
        }

        $today = now()->format('Y-m-d');

        // Cek apakah sudah presensi hari ini
        $exists = Absensi::where('user_id', $user->id)
                         ->where('tanggal', $today)
                         ->where('status', 'Hadir')
                         ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan presensi hari ini.']);
        }

        // Simpan sebagai hadir
        Absensi::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => $today],
            ['jam_masuk' => now(), 'status' => 'Hadir']
        );

        return response()->json(['success' => true, 'message' => 'Presensi berhasil dicatat']);
    }

    public function showScanner()
    {
        return view('absensi.scan');
    }

    public function showConfirmation(Request $request)
    {
        $token = $request->query('token');
        $date = now()->format('Y-m-d');
        $timeBlock = floor(time() / 600); // 10 menit
        
        $expectedToken = md5($date . $timeBlock . config('app.key'));
        $previousToken = md5($date . ($timeBlock - 1) . config('app.key'));

        if (!$token || ($token !== $expectedToken && $token !== $previousToken)) {
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
        // Simple token: md5(date + timeblock + APP_KEY)
        $date = now()->format('Y-m-d');
        $timeBlock = floor(time() / 600); // 10 menit
        $token = md5($date . $timeBlock . config('app.key'));
        
        return view('absensi.qr', compact('token'));
    }

    public function processQR(Request $request)
    {
        $request->validate(['token' => 'required']);
        
        $today = now()->format('Y-m-d');
        $timeBlock = floor(time() / 600);
        $expectedToken = md5($today . $timeBlock . config('app.key'));
        $previousToken = md5($today . ($timeBlock - 1) . config('app.key'));

        if ($request->token !== $expectedToken && $request->token !== $previousToken) {
            $msg = 'QR Code tidak valid atau sudah kadaluarsa.';
            if ($request->ajax()) return response()->json(['success' => false, 'message' => $msg]);
            return redirect()->back()->with('error', $msg);
        }

        $user = Auth::user();

        // Logic Absen Tunggal
        $absen = Absensi::where('user_id', $user->id)
                        ->where('tanggal', $today)
                        ->first();

        if ($absen && $absen->status === 'Hadir') {
            $msg = 'Anda sudah melakukan presensi hari ini.';
            if ($request->ajax()) return response()->json(['success' => false, 'message' => $msg]);
            return redirect()->back()->with('error', $msg);
        }

        Absensi::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => $today],
            [
                'jam_masuk' => now(),
                'status' => 'Hadir'
            ]
        );
        
        $msg = 'Presensi berhasil tercatat.';
        $type = 'presence';

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg, 'type' => $type]);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'status' => 'required|string'
        ]);

        Absensi::updateOrCreate(
            ['user_id' => $request->user_id, 'tanggal' => $request->tanggal],
            [
                'status' => $request->status,
                'jam_masuk' => null,
                'jam_keluar' => null
            ]
        );

        return response()->json(['success' => true, 'message' => 'Status presensi berhasil diperbarui.']);
    }
}
