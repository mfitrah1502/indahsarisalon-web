<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {

        $query = User::whereIn('role', ['admin', 'karyawan']); // ambil admin & kasir


        // Jika ada pencarian
        if($request->has('search') && $request->search != ''){
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $karyawans = $query->get();

        return view('karyawan.index', compact('karyawans'));
    }

    public function create()
    {
        return view('karyawan.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|unique:users',
        'email' => 'required|email|unique:users',
        'phone' => 'required|string|max:15',
        'password' => 'required|string|min:6',
        'role' => 'required|in:admin,karyawan', 
        'kategori' => 'required_if:role,karyawan|in:senior,junior',
    ]);

    User::create([
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
        'role' => $request->role,       // simpan role dari form
        'type' => 'karyawan',
         'kategori' => $request->role === 'karyawan' ? $request->kategori : null,
        'status' => $request->status ?? 'aktif',
    ]);

    return redirect()->route('karyawan.index')->with('success','Karyawan berhasil ditambahkan');
}

    public function edit(User $karyawan)
    {
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, User $karyawan)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|unique:users,username,'.$karyawan->id,
        'email' => 'required|email|unique:users,email,'.$karyawan->id,
        'phone' => 'required|string|max:15',
        'role' => 'required|in:admin,karyawan', // validasi role
        'kategori' => 'required_if:role,karyawan|in:senior,junior',
    ]);

    $karyawan->update([
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
        'phone' => $request->phone,
        'role' => $request->role,   
        'kategori' => $request->role === 'karyawan' ? $request->kategori : null,
        'type' => 'karyawan',    // update role
        'status' => $request->status ?? 'aktif',
    ]);

    return redirect()->route('karyawan.index')->with('success','Karyawan berhasil diupdate');
}

    public function destroy(User $karyawan)
    {
        $karyawan->delete();
        return redirect()->route('karyawan.index')->with('success','Karyawan berhasil dihapus');
    }
    public function absensi($id)
    {
        $karyawan = User::findOrFail($id);
        $startDate = now()->startOfMonth();
        $endDate = now();
        
        // Ambil data absensi yang ada di database
        $absensi = $karyawan->absensi()
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy('tanggal');

        // Ambil data hari libur
        $holidays = \App\Models\Holiday::whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        $report = [];
        // Iterasi dari hari ini ke belakang sampai awal bulan
        for ($date = clone $endDate; $date->gte($startDate); $date->subDay()) {
            $dateString = $date->format('Y-m-d');
            
            if (isset($absensi[$dateString])) {
                $item = $absensi[$dateString];
                
                // Jika hanya ada jam masuk dan sudah lewat hari, nyatakan tidak absensi pulang
                if ($dateString < $endDate->format('Y-m-d') && is_null($item->jam_keluar)) {
                    $item->status = 'Tidak Absensi Pulang';
                }
                
                $report[] = $item;
            } else {
                // Jika tidak ada record di hari sebelumnya dan bukan hari libur/Minggu, nyatakan Tidak Hadir
                $isWeekend = $date->dayOfWeek === 0; // 0 = Sunday
                $isHoliday = in_array($dateString, $holidays);
                
                if ($dateString < $endDate->format('Y-m-d') && !$isWeekend && !$isHoliday) {
                    $report[] = [
                        'tanggal' => $dateString,
                        'jam_masuk' => null,
                        'jam_keluar' => null,
                        'status' => 'Tidak Hadir'
                    ];
                }
            }
        }

        return response()->json($report);
    }

    public function filter(Request $request)
    {
        $query = User::whereIn('role', ['admin', 'karyawan']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $karyawans = $query->get();

        return view('karyawan.table', compact('karyawans'));
    }
}