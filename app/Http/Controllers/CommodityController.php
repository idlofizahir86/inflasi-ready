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
