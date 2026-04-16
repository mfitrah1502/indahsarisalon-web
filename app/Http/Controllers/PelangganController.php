<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'pelanggan');

        // fitur search
        if($request->has('search') && $request->search != ''){
            $query->where(function($q) use ($request){
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $pelanggans = $query->get();

        return view('pelanggan.index', compact('pelanggans'));
    }
    public function create()
    {
        return view('pelanggan.create'); // form tambah pelanggan
    }
    public function edit(User $pelanggan)
{
    return view('pelanggan.edit', compact('pelanggan')); // form edit
}
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:15',
            'password' => 'required|string|min:6|confirmed',
            'status'   => 'required|in:aktif,tidak',
        ]);

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'pelanggan', // selalu pelanggan
            'type'     => 'pelanggan',
            'status'   => $request->status,
        ]);

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan');
    }
    public function update(Request $request, User $pelanggan)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $pelanggan->id,
            'email'    => 'required|email|unique:users,email,' . $pelanggan->id,
            'password' => 'nullable|string|min:6|confirmed',
            'status'   => 'required|in:aktif,tidak',
        ]);

        $pelanggan->name     = $request->name;
        $pelanggan->username = $request->username;
        $pelanggan->email    = $request->email;
        $pelanggan->phone    = $request->phone;
        $pelanggan->status   = $request->status;
        $pelanggan->type     = 'pelanggan';

        if ($request->password) {
            $pelanggan->password = Hash::make($request->password);
        }

        $pelanggan->save();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diupdate');
    }

    // public function show($id)
    // {
    //     $pelanggan = User::where('role','pelanggan')->findOrFail($id);

    //     return view('pelanggan.show', compact('pelanggan'));
    // }

    public function destroy(User $pelanggan)
    {
        $pelanggan->delete();

        return redirect()->route('pelanggan.index')
            ->with('success','Pelanggan berhasil dihapus');
    }

    public function filter(Request $request)
    {
        $query = User::where('role', 'pelanggan');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $pelanggans = $query->get();

        return view('pelanggan.table', compact('pelanggans'));
    }
}