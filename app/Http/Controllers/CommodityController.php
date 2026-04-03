<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\Region;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CommodityController extends Controller
{
    public function index(Request $request) 
{
    return Inertia::render('DataCenter', [
        // Ambil semua wilayah untuk dropdown di Vue
        'regions' => Region::select('id', 'name', 'slug')->get(),
        
        // Ambil komoditas dengan filter wilayah jika ada
        'commodities' => Commodity::with('region')
            ->when($request->region, function ($query, $slug) {
                $query->whereHas('region', fn($q) => $q->where('slug', $slug));
            })
            ->latest()
            ->get(),
            
        'filters' => $request->only(['region'])
    ]);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric',
            'status' => 'required|in:stable,rising,falling',
        ]);

        Commodity::create($validated);

        return redirect()->back();
    }

    // API endpoint untuk get all commodities
    public function list()
    {
        try {
            // Try to fetch from database first
            $commodities = Commodity::select('id', 'name', 'category', 'price', 'status')
                ->orderBy('name')
                ->get();
            
            // If empty, return mock data for testing
            if ($commodities->isEmpty()) {
                $commodities = collect([
                    (object)['id' => 1, 'name' => 'Beras Medium', 'category' => 'Beras', 'price' => 12500, 'status' => 'stable'],
                    (object)['id' => 2, 'name' => 'Cabai Merah', 'category' => 'Bumbu', 'price' => 85000, 'status' => 'rising'],
                    (object)['id' => 3, 'name' => 'Minyak Goreng', 'category' => 'Minyak', 'price' => 13000, 'status' => 'stable'],
                    (object)['id' => 4, 'name' => 'Bawang Merah', 'category' => 'Bumbu', 'price' => 45000, 'status' => 'falling'],
                    (object)['id' => 5, 'name' => 'Daging Ayam', 'category' => 'Daging', 'price' => 38000, 'status' => 'stable'],
                    (object)['id' => 6, 'name' => 'Telur Ayam', 'category' => 'Telur', 'price' => 28000, 'status' => 'rising'],
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => $commodities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function dataCenter(Request $request)
    {
        $query = Commodity::query();

        // Filter berdasarkan wilayah
        if ($request->filled('region')) {
            $query->whereHas('region', function($q) use ($request) {
                $q->where('slug', $request->region);
            });
        }

        // Filter berdasarkan pencarian nama
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return Inertia::render('DataCenter', [
            'commodities' => $query->latest()->get(),
            'regions' => Region::select('id', 'name', 'slug')->get(),
            'filters' => $request->all(['search', 'region'])
        ]);
    }
}
