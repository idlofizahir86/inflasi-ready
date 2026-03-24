<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SimulationController extends Controller
{
    public function index()
    {
        // Ambil data komoditas terbaru per kategori (atau sesuai filter Anda)
        // Kita gunakan keyBy('name') agar mudah diakses di Vue
        $commodities = Commodity::select('name', 'price', 'category')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('commodities')
                    ->groupBy('category');
            })
            ->get()
            ->keyBy('name');

        return Inertia::render('Simulation', [
            'db_commodities' => $commodities,
            'baseline_inflation' => 4.20 
        ]);
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'fuel_increase' => 'required|numeric|min:0',
            'logistic_factor' => 'required|numeric'
        ]);

        $commodities = Commodity::all();
        
        // Logika Sederhana: Dampak Inflasi
        // Prediksi = Harga Asli * (1 + (Kenaikan BBM * Faktor Logistik / 100))
        $results = $commodities->map(function ($item) use ($request) {
            $impact = ($request->fuel_increase * $request->logistic_factor) / 100;
            return [
                'name' => $item->name,
                'current_price' => $item->price,
                'predicted_price' => $item->price * (1 + $impact),
                'increase_amount' => $item->price * $impact
            ];
        });

        return response()->json([
            'simulation_results' => $results,
            'overall_inflation_estimate' => $request->fuel_increase * $request->logistic_factor
        ]);
    }
}