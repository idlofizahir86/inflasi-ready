<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\Region;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Auto-login demo
        if (!Auth::check()) {
            $demoUser = User::where('email', 'akun-demo@pidi.id')->first();
            if ($demoUser) Auth::login($demoUser, true);
        }

        $allRegions = Region::with(['commodities', 'markets'])->orderBy('name')->get();
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

        // CARI TOP RISING: Urutkan berdasarkan trend tertinggi
        // Kita asumsikan format trend di DB adalah string seperti "+5.2%" atau "-2.1%"
        $topItem = $rawItems->sortByDesc(function($item) {
            return (float) str_replace(['+', '%'], '', $item->trend);
        })->first();

        return Inertia::render('Dashboard', [
            'all_regions' => $allRegions->map(fn($region) => $this->formatRegionForModal($region)),
            'selected_region_id' => $selectedRegionId ?? 'national',
            'stats' => [
                'inflation' => '3.2%',
                'region_display' => $selectedRegionId && $selectedRegionId !== 'national' 
                                ? ($allRegions->find($selectedRegionId)->name ?? 'Nasional') 
                                : 'Indonesia (Nasional)',
                'top_name' => $topItem->name ?? 'Tidak Ada Data',
                'top_price' => $topItem->price ?? 0, // Kirim angka mentah saja
                'top_trend' => $topItem->trend ?? '0%',
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

    private function formatRegionForModal($region) {
        // Hitung inflation rate region dari commodity trends
        $commodities = $region->commodities;
        $avgInflation = 0;
        if ($commodities->count() > 0) {
            $totalTrend = $commodities->sum(function($item) {
                return (float) str_replace(['+', '%'], '', $item->trend ?? '0');
            });
            $avgInflation = round($totalTrend / $commodities->count(), 2);
        }

        // Ambil top commodity di region ini
        $topCommodity = $commodities->sortByDesc(function($item) {
            return (float) str_replace(['+', '%'], '', $item->trend);
        })->first();

        // Format markets untuk modal
        $markets = $region->markets()
            ->latest()
            ->take(5)
            ->get()
            ->map(function($market) {
                return [
                    'name' => $market->name,
                    'price' => 'Rp ' . number_format($market->latest_price, 0, ',', '.'),
                    'time' => $market->updated_at->diffForHumans(),
                ];
            })
            ->toArray();

        // Volatility calculation dari price variance
        $priceVariance = $commodities->isEmpty() ? 0 : 
            round($commodities->avg(function($item) {
                return abs((float) str_replace(['+', '%'], '', $item->trend ?? '0'));
            }), 2);

        return [
            'id' => $region->id,
            'name' => $region->name,
            'slug' => $region->slug ?? Str::slug($region->name),
            'status' => $region->status ?? 'STABLE',
            'color' => $region->color ?? 'bg-primary',
            'inflation' => $avgInflation . '%',
            'volatility' => $priceVariance . '%',
            'markets_count' => $region->markets()->count(),
            'top_commodity' => $topCommodity->name ?? 'Tidak Ada Data',
            'top_price' => $topCommodity ? 'Rp ' . number_format($topCommodity->price, 0, ',', '.') : '0',
            'marketList' => $markets,
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