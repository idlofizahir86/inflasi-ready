<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\Region;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Auto-login demo
        if (!Auth::check()) {
            $demoUser = User::where('email', 'akun-demo@pidi.id')->first();
            if ($demoUser) Auth::login($demoUser, true);
        }

        $allRegions = Region::orderBy('name')->get();
        $selectedRegionId = $request->input('region_id'); // Bisa null (Nasional)

        // Query Utama Komoditas
        $query = Commodity::query();
        
        // Jika pilih wilayah tertentu, filter. Jika tidak (Nasional), ambil semua.
        if ($selectedRegionId && $selectedRegionId !== 'national') {
            $query->where('region_id', $selectedRegionId);
        }

        $rawItems = $query->latest()->get();

        // Data untuk 4 Card Utama (Unik per Kategori)
        $displayItems = $rawItems->unique('category')->take(4);
        
        // Data untuk "Full Report" (Lebih banyak & Unik per Kategori)
        $fullReportItems = $rawItems->unique('name')->values();

        $topItem = $displayItems->first();

        return Inertia::render('Dashboard', [
            'all_regions' => $allRegions,
            'selected_region_id' => $selectedRegionId ?? 'national',
            'stats' => [
                'inflation' => '3.2%',
                'region_name' => $selectedRegionId && $selectedRegionId !== 'national' 
                                 ? $allRegions->find($selectedRegionId)->name 
                                 : 'Indonesia (Nasional)',
                'top_name' => $topItem->name ?? 'N/A',
                'top_price' => number_format($topItem->price ?? 0, 0, ',', '.'),
                'top_trend' => $topItem->trend ?? 'STABLE',
            ],
            'db_commodities' => $displayItems->values()->map(fn($item) => $this->formatItem($item)),
            'full_report' => $fullReportItems->map(fn($item) => $this->formatItem($item)),
            'chart_data' => $this->generateChartData($displayItems)
        ]);
    }

    private function formatItem($item) {
        return [
            'name' => $item->name,
            'category' => $item->category,
            'price' => number_format($item->price, 0, ',', '.'),
            'trend' => $item->trend,
            'status' => $item->status,
            'region' => $item->region->name ?? 'Nasional'
        ];
    }

    private function generateChartData($items) {
        $chartData = [];
        foreach ($items as $commodity) {
            for ($i = 14; $i >= 0; $i--) { // 15 hari terakhir cukup untuk dashboard
                $chartData[] = [
                    'name' => $commodity->name,
                    'date' => now()->subDays($i)->format('d M'),
                    'price' => (int)$commodity->price + rand(-1000, 1000)
                ];
            }
        }
        return $chartData;
    }
}