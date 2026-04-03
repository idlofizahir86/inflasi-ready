<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierPrice;
use Illuminate\Database\Eloquent\Collection;

class SupplierMatchingService
{
    /**
     * Dapatkan rekomendasi supplier untuk simulasi tertentu
     * 
     * @param int $commodityId
     * @param float $simulatedPrice Target price dari simulasi
     * @param float $userLat User latitude (optional, untuk jarak)
     * @param float $userLong User longitude (optional, untuk jarak)
     * @param int $limit
     */
    public function recommendSuppliers(
        int $commodityId,
        float $simulatedPrice,
        ?float $userLat = null,
        ?float $userLong = null,
        int $limit = 5
    ): array {
        try {
            $query = Supplier::query()
                ->with(['prices' => function ($q) use ($commodityId) {
                    $q->where('commodity_id', $commodityId)
                        ->orderBy('price_date', 'desc')
                        ->limit(1);
                }])
                ->where('is_verified', true);

            $suppliers = $query->get();

            // Filter suppliers yang punya harga untuk komoditas ini
            $suppliersWithPrice = $suppliers->filter(function ($supplier) use ($commodityId) {
                return $supplier->prices->count() > 0;
            });

            // Transform dan hitung metrik
            $recommendations = $suppliersWithPrice->map(function ($supplier) use ($simulatedPrice, $userLat, $userLong) {
                $latestPrice = $supplier->prices->first();
                $priceDifference = $latestPrice->price_per_kg - $simulatedPrice;
                $savingsPercent = ($priceDifference / $simulatedPrice) * 100;

                $recommendation = [
                    'supplier_id' => $supplier->id,
                    'nama_supplier' => $supplier->nama_supplier,
                    'lokasi' => $supplier->lokasi,
                    'kontak' => $supplier->kontak,
                    'rating' => $supplier->rating,
                    'price' => (float) $latestPrice->price_per_kg,
                    'price_date' => $latestPrice->price_date->format('Y-m-d'),
                    'simulated_price' => (float) $simulatedPrice,
                    'price_difference' => (float) round($priceDifference, 2),
                    'savings_percent' => (float) round($savingsPercent, 2),
                    'is_cheaper' => $latestPrice->price_per_kg < $simulatedPrice,
                    'min_order_kg' => $supplier->min_order_kg,
                    'price_update_frequency' => $supplier->price_update_frequency,
                ];

                // Hitung jarak jika koordinat tersedia
                if ($userLat && $userLong) {
                    $distance = $supplier->calculateDistance($userLat, $userLong);
                    $recommendation['distance_km'] = $distance ? (float) round($distance, 2) : null;
                } else {
                    $recommendation['distance_km'] = null;
                }

                return $recommendation;
            })->toArray();

            // Sort berdasarkan harga (termurah dulu)
            usort($recommendations, function ($a, $b) {
                if ($a['price'] == $b['price']) {
                    return 0;
                }
                return $a['price'] < $b['price'] ? -1 : 1;
            });

            // Limit hasil
            $recommendations = array_slice($recommendations, 0, $limit);

            return [
                'success' => true,
                'count' => count($recommendations),
                'data' => $recommendations,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error saat mencari supplier: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Hitung potential savings jika menggunakan supplier tertentu
     */
    public function calculateSavings(
        float $baseOperationalCost,
        float $commodityPercentage,
        float $currentPrice,
        float $supplierPrice
    ): array {
        $commodityCost = $baseOperationalCost * $commodityPercentage;
        $pricePercentChange = (($supplierPrice - $currentPrice) / $currentPrice) * 100;
        $potentialSavings = $commodityCost * ($pricePercentChange / 100);

        return [
            'commodity_cost' => (float) round($commodityCost, 2),
            'price_per_unit_current' => (float) round($currentPrice, 2),
            'price_per_unit_supplier' => (float) round($supplierPrice, 2),
            'price_change_percent' => (float) round($pricePercentChange, 2),
            'potential_savings' => (float) round($potentialSavings, 2),
            'is_beneficial' => $potentialSavings < 0, // Negative = savings
        ];
    }

    /**
     * Get supplier score untuk ranking
     */
    public function calculateSupplierScore(array $supplierData): float
    {
        $score = 0;

        // Rating weight (0-30 points)
        if ($supplierData['rating']) {
            $score += ($supplierData['rating'] / 5) * 30;
        }

        // Harga weight (0-40 points) - lebih murah = score tinggi
        if ($supplierData['is_cheaper']) {
            $score += min(40, 40 * (abs($supplierData['savings_percent']) / 100));
        } else {
            // Jika lebih mahal, kurangi score
            $score -= min(20, 20 * ($supplierData['savings_percent'] / 100));
        }

        // Jarak weight (0-30 points) - lebih dekat = score tinggi
        if ($supplierData['distance_km'] !== null) {
            // Normalize: 0-50 km = full score
            $distanceScore = max(0, 30 - ($supplierData['distance_km'] / 50) * 30);
            $score += $distanceScore;
        }

        // Ensure score dalam range 0-100
        return (float) max(0, min(100, $score));
    }

    /**
     * Batch update supplier prices (untuk cron/scheduler)
     */
    public function updateSupplierPrices(array $priceData): array
    {
        $updated = 0;
        $failed = 0;

        foreach ($priceData as $data) {
            try {
                SupplierPrice::create([
                    'supplier_id' => $data['supplier_id'],
                    'commodity_id' => $data['commodity_id'],
                    'price_per_kg' => $data['price_per_kg'],
                    'price_date' => $data['price_date'] ?? now(),
                    'notes' => $data['notes'] ?? null,
                ]);

                $updated++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        return [
            'updated' => $updated,
            'failed' => $failed,
            'total' => $updated + $failed,
        ];
    }
}
