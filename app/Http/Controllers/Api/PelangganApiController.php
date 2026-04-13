<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PelangganApiController extends Controller
{
    public function index()
    {
        $data = User::where('role', 'pelanggan')->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $data = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'pelanggan',
            'type'     => 'pelanggan',
            'status'   => $request->status,
        ]);

        return response()->json($data, 201);
    }

    public function show($id)
    {
        $data = User::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $data = User::findOrFail($id);
        $data->update($request->all());

        return response()->json($data);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}