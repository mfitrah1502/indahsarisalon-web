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
        $query = Treatment::with('details', 'category'); // eager load detail dan kategori

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

        // Sort
        if($request->sort) {
            switch($request->sort) {
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'price_asc':
                    $query->withMin('details', 'price')->orderBy('details_min_price', 'asc');
                    break;
                case 'price_desc':
                    $query->withMin('details', 'price')->orderBy('details_min_price', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $treatments = $query->with('category', 'details')->paginate(10);

        // Jika kategori disimpan sebagai array di controller
        $categories = Category::all(); // Ambil semua kategori untuk filter dropdown
        $treatments->transform(function($treatment) {
    $treatment->details_for_modal = $treatment->details->map(function($d){
        return [
            'name' => $d->name,
            'duration' => $d->duration,
            'price' => $d->price,
            'description' => $d->description
        ];
    });
    return $treatment;
});

        return view('treatment.index', compact('treatments', 'categories'));
    }

    // Menampilkan form tambah treatment
    public function create()
    {
         $categories = Category::all(); // Ambil semua kategori untuk dropdown
        return view('treatment.create', compact('categories'));
    }

    // Menyimpan treatment baru
    public function store(Request $request)
    {
        $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'nullable|exists:categories,id',
        'category' => 'nullable|string',   // wajib ada input category di form
        'details.*.name' => 'required|string',
        'details.*.duration' => 'required|integer',
        'details.*.price' => 'required|numeric',
        'promo_type' => 'nullable|string',
        'promo_value' => 'nullable|numeric',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'allow_multi_select' => 'nullable|boolean',
    ]);

    
    if ($request->filled('category')) {
        // Kalau ada input kategori baru, buat kategori baru
        $category = Category::firstOrCreate(['name' => $request->category]);
        $category_id = $category->id;
    } elseif ($request->filled('category_id')) {
        // Kalau pilih dari dropdown
        $category_id = $request->category_id;
    } else {
        $category_id = null; // Tidak pilih kategori sama sekali
    }

    // Simpan treatment
    $treatment = new Treatment();
    $treatment->name = $request->name;
    $treatment->category_id = $category_id;
    $treatment->is_promo = $request->has('is_promo') ? 1 : 0;
    $treatment->promo_type = $request->promo_type;
    $treatment->promo_value = $request->promo_value;
    $treatment->allow_multi_select = $request->has('allow_multi_select') ? 1 : 0;
    // Upload gambar ke Supabase
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();

            $fileContents = file_get_contents($file->getRealPath());
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Content-Type' => 'application/octet-stream',
            ])->withBody($fileContents, 'application/octet-stream')
            ->post(env('SUPABASE_URL') . '/storage/v1/object/' . env('SUPABASE_BUCKET') . '/' . $filename, file_get_contents($file));

            if ($response->failed()) {
                return back()->withErrors(['image' => 'Gagal upload ke Supabase: ' . $response->body()]);
            }

            $treatment->image = $filename;
        }
    $treatment->save();

    // Simpan detail treatment
    foreach ($request->details as $detail) {
        $treatment->details()->create([
            'name' => $detail['name'],
            'duration' => $detail['duration'] ?? 0,
            'price' => $detail['price'] ?? 0,
            'description' => $detail['description'] ?? null,
            'has_stylist_price' => isset($detail['has_stylist_price']) ? 1 : 0,
            'price_senior' => $detail['price_senior'] ?? null,
            'price_junior' => $detail['price_junior'] ?? null,
        ]);

    }

    return redirect()->route('treatment.index')->with('success','Treatment berhasil ditambahkan');
}

    // Menampilkan form edit
    public function edit(Treatment $treatment)
    {
        $categories = Category::all(); // Ambil semua kategori untuk dropdown
        return view('treatment.edit', compact('treatment','categories'));
    }

    // Update treatment
    public function update(Request $request, Treatment $treatment)
    {
        // Update treatment utama
        $treatment->name = $request->name;
        $treatment->category_id = $request->category_id;
        $treatment->is_promo = $request->has('is_promo') ? 1 : 0;
        $treatment->promo_type = $request->promo_type;
        $treatment->promo_value = $request->promo_value;
        $treatment->allow_multi_select = $request->has('allow_multi_select') ? 1 : 0;
         // Upload gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama di Supabase (opsional)
            if ($treatment->image) {
                Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                    'apikey' => env('SUPABASE_SERVICE_KEY'),
                ])->delete(env('SUPABASE_URL') . '/storage/v1/object/' . env('SUPABASE_BUCKET') . '/' . $treatment->image);
            }

            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();

            $fileContents = file_get_contents($file->getRealPath());
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Content-Type' => 'application/octet-stream',
            ])->withBody($fileContents, 'application/octet-stream')
            ->post(env('SUPABASE_URL') . '/storage/v1/object/' . env('SUPABASE_BUCKET') . '/' . $filename, file_get_contents($file));

            if ($response->failed()) {
                return back()->withErrors(['image' => 'Gagal upload ke Supabase: ' . $response->body()]);
            }

            $treatment->image = $filename;
        }
        $treatment->save();

        // Hapus detail lama
        $treatment->details()->delete();

        // Simpan detail baru
        foreach ($request->details as $detail) {
            $treatment->details()->create([
                'name' => $detail['name'],
                'duration' => $detail['duration'],
                'price' => $detail['price'] ?? 0,
                'description' => $detail['description'] ?? null,
                'has_stylist_price' => isset($detail['has_stylist_price']) ? 1 : 0,
                'price_senior' => $detail['price_senior'] ?? null,
                'price_junior' => $detail['price_junior'] ?? null,
            ]);
        }

        // Redirect ke index dengan pesan sukses
        return redirect()->route('treatment.index')
                         ->with('success', 'Treatment berhasil diperbarui!');
    }

    // Hapus treatment
    public function destroy(Treatment $treatment)
    {
        $treatment->delete();
        return redirect()->route('treatment.index')->with('success','Treatment berhasil dihapus');
    }
    // app/Http/Controllers/TreatmentController.php

public function filter(Request $request)
{
    $query = Treatment::with(['details', 'category']); // <- tambahkan 'category'

    if ($request->category) {
        $query->whereHas('category', function($q) use ($request) {
            $q->where('name', $request->category);
        });
    }

    if ($request->search) {
        $query->where('name', 'like', "%{$request->search}%");
    }

    if ($request->sort) {
        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->withMin('details', 'price')->orderBy('details_min_price', 'asc');
                break;
            case 'price_desc':
                $query->withMin('details', 'price')->orderBy('details_min_price', 'desc');
                break;
        }
    } else {
        $query->orderBy('created_at', 'desc');
    }

    $treatments = $query->get(); // AJAX load

    return view('treatment.table', compact('treatments'));}
}