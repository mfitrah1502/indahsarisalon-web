<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

    // Mulai query builder
    $query = Produk::query();

    // Jika ada keyword, filter berdasarkan nama produk
    if ($search) {
        $query->where('nama', 'like', "%{$search}%");
    }

    // Eksekusi query
    $produks = $query->get();
        return view('produk.index', compact('produks', 'search'));
    }
public function create()
{
    return view('produk.create');
}
public function store(Request $request)
{
    $request->validate([
        'nama'  => 'required',
        'harga' => 'required|numeric',
        'stok'  => 'required|numeric'
    ]);

    Produk::create($request->all());

    return redirect()->route('produk.index')
        ->with('success', 'Data berhasil ditambahkan');
}

public function edit($id)
{
    $produk = Produk::findOrFail($id);
    return view('produk.edit', compact('produk'));
}
public function update(Request $request, $id)
{
    $request->validate([
        'nama'  => 'required',
        'harga' => 'required|numeric',
        'stok'  => 'required|numeric'
    ]);

    $produk = Produk::findOrFail($id);
    $produk->update($request->all());

    return redirect()->route('produk.index')
        ->with('success', 'Data berhasil diupdate');
}

public function destroy($id)
{
    $produk = Produk::findOrFail($id);
    $produk->delete();

    return redirect()->route('produk.index')
        ->with('success', 'Data berhasil dihapus');
}
}