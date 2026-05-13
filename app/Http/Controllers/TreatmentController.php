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
        // 1. Inisialisasi Query dengan Join Kategori dulu agar select tidak tertimpa
        $query = Treatment::with('details')
            ->leftJoin('categories', 'treatments.category_id', '=', 'categories.id')
            ->select('treatments.*', 'categories.name as category_name');

        // 2. Tambahkan perhitungan agregat (Min, Max, Count)
        $query->withMin('details', 'price')
              ->withMax('details', 'price')
              ->withCount('details');

        // 3. Filter kategori
        if($request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        // 4. Search nama
        if($request->search) {
            $query->where('treatments.name', 'like', "%{$request->search}%");
        }

        // 5. Sorting
        $query->orderByRaw("CASE WHEN categories.name = 'Promo' THEN 0 ELSE 1 END")
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
                    $query->orderBy('details_min_price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('details_min_price', 'desc');
                    break;
            }
        }

        $treatments = $query->with(['category'])->paginate(10);
        $categories = Category::select('id', 'name')->get(); 
        
        $customers = \App\Models\User::select('id', 'name', 'phone')
            ->where('role', 'pelanggan')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->limit(500)
            ->get();

        return view('treatment.index', compact('treatments', 'categories', 'customers'));
    }

    // Mendapatkan detail treatment untuk modal (AJAX) - Pastikan fungsi ini di luar index
    public function getDetails($id)
    {
        try {
            // Gunakan query mentah (DB::table) untuk menghindari error model/appends
            $details = DB::table('treatment_details')
                ->where('treatment_id', $id)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $details
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data mentah: ' . $e->getMessage()
            ], 500);
        }
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
            
            Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Content-Type' => 'application/octet-stream',
            ])->withBody($fileContents, 'application/octet-stream')
            ->post(env('SUPABASE_URL') . '/storage/v1/object/' . env('SUPABASE_BUCKET') . '/' . $filename);

            $treatment->image = $filename;
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

            Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Content-Type' => 'application/octet-stream',
            ])->withBody($fileContents, 'application/octet-stream')
            ->post(env('SUPABASE_URL') . '/storage/v1/object/' . env('SUPABASE_BUCKET') . '/' . $filename);

            $treatment->image = $filename;
        }
        
        $treatment->save();

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
        $query = Treatment::leftJoin('categories', 'treatments.category_id', '=', 'categories.id')
            ->select('treatments.*', 'categories.name as category_name')
            ->withMin('details', 'price')
            ->withMax('details', 'price')
            ->withCount('details');

        if ($request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        if ($request->search) {
            $query->where('treatments.name', 'like', "%{$request->search}%");
        }

        $query->orderByRaw("CASE WHEN categories.name = 'Promo' THEN 0 ELSE 1 END")
              ->orderBy('treatments.created_at', 'desc');

        $treatments = $query->get();
        return view('treatment.table', compact('treatments'));
    }

    public function broadcastPromo(Request $request)
    {
        return back()->with('success', "Fitur broadcast dalam pengembangan.");
    }
}