<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Region;

// Simulate DashboardController's data sending
$allRegions = Region::with(['commodities', 'markets'])->orderBy('name')->get();

// Simulate formatRegionForModal from controller
$formattedRegions = $allRegions->map(function($region) {
    $commodities = $region->commodities;
    $avgInflation = 0;
    if ($commodities->count() > 0) {
        $totalTrend = $commodities->sum(function($item) {
            return (float) str_replace(['+', '%'], '', $item->trend ?? '0');
        });
        $avgInflation = round($totalTrend / $commodities->count(), 2);
    }

    $topCommodity = $commodities->sortByDesc(function($item) {
        return (float) str_replace(['+', '%'], '', $item->trend);
    })->first();

    $allMarkets = $region->markets;
    $markets = $allMarkets->sortByDesc('updated_at')
        ->take(5)
        ->map(function($market) {
            return [
                'name' => $market->name,
                'price' => 'Rp ' . number_format($market->latest_price, 0, ',', '.'),
                'time' => $market->updated_at->diffForHumans(),
            ];
        })
        ->values()
        ->toArray();

    $priceVariance = $commodities->isEmpty() ? 0 : 
        round($commodities->avg(function($item) {
            return abs((float) str_replace(['+', '%'], '', $item->trend ?? '0'));
        }), 2);

    return [
        'id' => $region->id,
        'name' => $region->name,
        'status' => $region->status ?? 'STABLE',
        'color' => $region->color ?? 'bg-primary',
        'inflation' => $avgInflation . '%',
        'volatility' => $priceVariance . '%',
        'markets_count' => $allMarkets->count(),
        'top_commodity' => $topCommodity->name ?? 'Tidak Ada Data',
        'top_price' => $topCommodity ? 'Rp ' . number_format($topCommodity->price, 0, ',', '.') : 'Rp 0',
        'marketList' => $markets,
    ];
})->take(3);

echo "=== Sample formatted regions (first 3) ===" . PHP_EOL;
echo json_encode($formattedRegions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
