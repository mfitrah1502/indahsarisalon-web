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
        $query = User::whereIn('role', ['admin', 'karyawan']);


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
        'role' => 'required|in:owner,admin,karyawan', 

        'nickname' => 'nullable|string|max:255',
        'birth_place' => 'nullable|string|max:255',
        'birth_date' => 'nullable|date',
        'gender' => 'nullable|string|max:50',
        'position' => 'nullable|string|max:255',
        'division' => 'nullable|string|max:255',
        'join_date' => 'nullable|date',
        'employment_status' => 'nullable|string|max:100',
        'emergency_contact' => 'nullable|string|max:50',
        'bank_account_name' => 'nullable|string|max:255',
        'bank_account_number' => 'nullable|string|max:50',
        'last_education' => 'nullable|string|max:255',
    ]);

        $kategori = null;
        if ($request->filled('position')) {
            $pos = strtolower($request->position);
            if (str_contains($pos, 'senior') || str_contains($pos, 'creative')) {
                $kategori = 'senior';
            } elseif (str_contains($pos, 'junior')) {
                $kategori = 'junior';
            }
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,       // simpan role dari form
            'type' => 'karyawan',
            'kategori' => $kategori,
            'status' => $request->status ?? 'aktif',
            'nickname' => $request->nickname,
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'position' => $request->position,
            'division' => $request->division,
            'join_date' => $request->join_date,
            'employment_status' => $request->employment_status,
            'emergency_contact' => $request->emergency_contact,
            'bank_account_name' => $request->bank_account_name,
            'bank_account_number' => $request->bank_account_number,
            'last_education' => $request->last_education,
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
            'role' => 'required|in:owner,admin,karyawan', // validasi role

            'nickname' => 'nullable|string|max:255',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'division' => 'nullable|string|max:255',
            'join_date' => 'nullable|date',
            'employment_status' => 'nullable|string|max:100',
            'emergency_contact' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'last_education' => 'nullable|string|max:255',
        ]);

        $kategori = null;
        if ($request->filled('position')) {
            $pos = strtolower($request->position);
            if (str_contains($pos, 'senior') || str_contains($pos, 'creative')) {
                $kategori = 'senior';
            } elseif (str_contains($pos, 'junior')) {
                $kategori = 'junior';
            }
        }

        $karyawan->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,   
            'kategori' => $kategori,
            'type' => 'karyawan',    // update role
            'status' => $request->status ?? 'aktif',
            'nickname' => $request->nickname,
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'position' => $request->position,
            'division' => $request->division,
            'join_date' => $request->join_date,
            'employment_status' => $request->employment_status,
            'emergency_contact' => $request->emergency_contact,
            'bank_account_name' => $request->bank_account_name,
            'bank_account_number' => $request->bank_account_number,
            'last_education' => $request->last_education,
        ]);

    return redirect()->route('karyawan.index')->with('success','Karyawan berhasil diupdate');
}

    public function destroy(User $karyawan)
    {
        // 1. Cek riwayat booking sebagai stylist
        $hasStylistBookings = \App\Models\Booking::where('stylist_id', $karyawan->id)->exists();
        
        // 2. Cek riwayat booking sebagai kasir
        $hasCashierBookings = \App\Models\Booking::where('cashier_id', $karyawan->id)->exists();

        // 3. Cek riwayat absensi
        $hasAbsensi = $karyawan->absensi()->exists();

        if ($hasStylistBookings || $hasCashierBookings || $hasAbsensi) {
            return redirect()->route('karyawan.index')->with('error', 'Karyawan ini tidak dapat dihapus karena memiliki riwayat booking/transaksi atau absensi. Silakan ubah status karyawan menjadi "nonaktif" melalui menu edit.');
        }

        try {
            $karyawan->delete();
            return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('karyawan.index')->with('error', 'Karyawan ini tidak dapat dihapus karena terikat dengan data lainnya di database. Anda dapat menonaktifkan statusnya saja.');
        }
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
    {        $query = User::whereIn('role', ['admin', 'karyawan']);

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