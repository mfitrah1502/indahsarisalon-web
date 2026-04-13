<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $request->name,
        ]);
        if ($request->ajax()) {
        return response()->json($category);
    }

        return redirect()->back()->with('success', "Kategori '{$category->name}' berhasil ditambahkan!");
    }
    public function update(Request $request, $id)
{
    $category = Category::findOrFail($id);
    $category->update([
        'name' => $request->name
    ]);

    return response()->json($category);
}

public function destroy($id)
{
    Category::findOrFail($id)->delete();

    return response()->json(['success' => true]);
}
}