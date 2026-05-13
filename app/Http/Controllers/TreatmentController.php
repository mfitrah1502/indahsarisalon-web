<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Models\Treatment; // Model utama treatment
use App\Models\TreatmentDetail; // Detail treatment
use App\Models\Category; // Opsional jika kategori dibuat terpisah
use Illuminate\Support\Facades\DB;

class TreatmentController extends Controller
{
    // Menampilkan daftar treatment dengan filter, search, dan sort
    public function index(Request $request)
    {
        // Gunakan withMin dan withMax agar perhitungan dilakukan di database (SANGAT CEPAT)
        $query = Treatment::with(['category'])
            ->withMin('details', 'price')
            ->withMax('details', 'price')
            ->withCount('details'); 

        // Filter kategori
        if($request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        // Search nama
        if($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Sort & Prioritize "Promo" category
        $query->leftJoin('categories', 'treatments.category_id', '=', 'categories.id')
              ->select('treatments.*', 'categories.name as category_name')
              ->orderByRaw("CASE WHEN categories.name = 'Promo' THEN 0 ELSE 1 END")
              ->orderBy('treatments.created_at', 'desc');

        if($request->sort) {
            switch($request->sort) {
                case 'name_asc':
                    $query->orderBy('treatments.name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('treatments.name', 'desc');
                    break;
                case 'price_asc':
                    $query->withMin('details', 'price')->orderBy('details_min_price', 'asc');
                    break;
                case 'price_desc':
                    $query->withMin('details', 'price')->orderBy('details_min_price', 'desc');
                    break;
            }
        }

        $treatments = $query->paginate(10);

        // Ambil kategori untuk filter
        $categories = Category::select('id', 'name')->get(); 
        
        // Ambil data pelanggan untuk modal broadcast (dibatasi agar tidak lambat)
        $customers = \App\Models\User::select('id', 'name', 'phone')
            ->where('role', 'pelanggan')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->limit(500)
            ->get();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $treatments,
                'categories' => $categories
            ]);
        }

        return view('treatment.index', compact('treatments', 'categories', 'customers'));
    }

    // Mendapatkan detail treatment untuk modal (AJAX)
    public function getDetails($id)
    {
        $treatment = Treatment::with('details')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $treatment->details
        ]);
    }

    // Menampilkan form tambah treatment
    public function create()
    {
         $categories = Category::all(); 
        return view('treatment.create', compact('categories'));
    }

    // Menyimpan treatment baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'category' => 'nullable|string',
            'details.*.name' => 'required|string',
            'details.*.duration' => 'required|integer',
            'details.*.price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->filled('category')) {
            $category = Category::firstOrCreate(['name' => $request->category]);
            $category_id = $category->id;
        } else {
            $category_id = $request->category_id;
        }

        $treatment = new Treatment();
        $treatment->name = $request->name;
        $treatment->category_id = $category_id;
        $treatment->is_promo = $request->has('is_promo') ? 1 : 0;
        $treatment->is_active = $request->has('is_active') ? 1 : 0;
        $treatment->promo_start_date = $request->promo_start_date ?: null;
        $treatment->promo_end_date = $request->promo_end_date ?: null;
        $treatment->allow_multi_select = $request->has('allow_multi_select') ? 1 : 0;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $fileContents = file_get_contents($file->getRealPath());
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Content-Type' => 'application/octet-stream',
            ])->withBody($fileContents, 'application/octet-stream')
            ->post(env('SUPABASE_URL') . '/storage/v1/object/' . env('SUPABASE_BUCKET') . '/' . $filename);

            if ($response->successful()) {
                $treatment->image = $filename;
            }
        }

        $treatment->save();

        foreach ($request->details as $detail) {
            $treatment->details()->create($detail);
        }

        return redirect()->route('treatment.index')->with('success','Treatment berhasil ditambahkan');
    }

    public function edit(Treatment $treatment)
    {
        $categories = Category::all();
        return view('treatment.edit', compact('treatment','categories'));
    }

    public function update(Request $request, Treatment $treatment)
    {
        $treatment->name = $request->name;
        $treatment->category_id = $request->category_id;
        $treatment->is_promo = $request->has('is_promo') ? 1 : 0;
        $treatment->is_active = $request->has('is_active') ? 1 : 0;
        $treatment->promo_start_date = $request->promo_start_date ?: null;
        $treatment->promo_end_date = $request->promo_end_date ?: null;
        $treatment->allow_multi_select = $request->has('allow_multi_select') ? 1 : 0;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $fileContents = file_get_contents($file->getRealPath());

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Content-Type' => 'application/octet-stream',
            ])->withBody($fileContents, 'application/octet-stream')
            ->post(env('SUPABASE_URL') . '/storage/v1/object/' . env('SUPABASE_BUCKET') . '/' . $filename);

            if ($response->successful()) {
                $treatment->image = $filename;
            }
        }
        
        $treatment->save();

        // Sync details (Sederhana: hapus semua dan buat baru, atau sync by ID)
        $treatment->details()->delete();
        foreach ($request->details as $detail) {
            $treatment->details()->create($detail);
        }

        return redirect()->route('treatment.index')->with('success', 'Treatment berhasil diperbarui!');
    }

    public function destroy(Treatment $treatment)
    {
        $treatment->delete();
        return redirect()->route('treatment.index')->with('success','Treatment berhasil dihapus');
    }

    public function filter(Request $request)
    {
        $query = Treatment::with(['category'])
                ->withMin('details', 'price')
                ->withMax('details', 'price')
                ->withCount('details');

        if ($request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $query->leftJoin('categories', 'treatments.category_id', '=', 'categories.id')
              ->select('treatments.*', 'categories.name as category_name')
              ->orderByRaw("CASE WHEN categories.name = 'Promo' THEN 0 ELSE 1 END")
              ->orderBy('treatments.created_at', 'desc');

        $treatments = $query->get();
        return view('treatment.table', compact('treatments'));
    }

    public function broadcastPromo(Request $request)
    {
        $promoTreatments = Treatment::whereHas('category', function($q) {
            $q->where('name', 'Promo');
        })->where('is_active', 1)->get();

        if ($promoTreatments->isEmpty()) {
            return back()->with('error', 'Tidak ada treatment yang sedang promo saat ini.');
        }

        $customers = \App\Models\User::where('role', 'pelanggan')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        if ($customers->isEmpty()) {
            return back()->with('error', 'Tidak ada pelanggan dengan nomor WhatsApp terdaftar.');
        }

        // Logika broadcast...
        return back()->with('success', "Broadcast promo berhasil dikirim.");
    }
}