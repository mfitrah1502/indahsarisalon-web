<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'desc')->get();
        return view('holidays.index', compact('holidays'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|unique:holidays,date',
            'description' => 'nullable|string|max:255',
        ]);

        Holiday::create($request->all());

        return redirect()->route('holidays.index')->with('success', 'Hari libur berhasil ditambahkan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return redirect()->route('holidays.index')->with('success', 'Hari libur berhasil dihapus.');
    }
}
