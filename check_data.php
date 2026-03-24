<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Region;
use App\Models\Market;
use App\Models\Commodity;

echo "=== Database Record Counts ===" . PHP_EOL;
echo "Regions: " . Region::count() . PHP_EOL;
echo "Markets: " . Market::count() . PHP_EOL;
echo "Commodities: " . Commodity::count() . PHP_EOL;

echo "\n=== First Region with Relations ===" . PHP_EOL;
$region = Region::with(['commodities', 'markets'])->first();

if ($region) {
    echo "Region ID: " . $region->id . PHP_EOL;
    echo "Region Name: " . $region->name . PHP_EOL;
    echo "Commodities Count: " . $region->commodities->count() . PHP_EOL;
    echo "Markets Count: " . $region->markets->count() . PHP_EOL;
    
    if ($region->markets->count() > 0) {
        echo "\nFirst 3 Markets:" . PHP_EOL;
        foreach ($region->markets->take(3) as $market) {
            echo "  - " . $market->name . " (Rp " . number_format($market->latest_price, 0) . ")" . PHP_EOL;
        }
    }
    
    if ($region->commodities->count() > 0) {
        echo "\nFirst 3 Commodities:" . PHP_EOL;
        foreach ($region->commodities->take(3) as $commodity) {
            echo "  - " . $commodity->name . " (Trend: " . $commodity->trend . ")" . PHP_EOL;
        }
    }
}

echo "\n=== Sample formatted region data ===" . PHP_EOL;
$testRegion = Region::with(['commodities', 'markets'])->first();
if ($testRegion) {
    $formatted = [
        'id' => $testRegion->id,
        'name' => $testRegion->name,
        'inflation' => '2.5%',
        'volatility' => '3.2%',
        'markets_count' => $testRegion->markets->count(),
        'top_commodity' => 'Beras Medium',
        'marketList_count' => count($testRegion->markets->take(5)->toArray()),
    ];
    echo json_encode($formatted, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
