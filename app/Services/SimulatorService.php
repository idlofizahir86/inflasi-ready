<?php

namespace App\Services;

use App\Models\Commodity;

class SimulatorService
{
    /**
     * Kalkulasi dampak simulasi harga terhadap biaya operasional UMKM
     * 
     * @param array $params {
     *     commodity_id: int,
     *     base_price: float,
     *     simulated_price: float,
     *     weight_in_basket: float (0-1, persentase dalam keranjang),
     *     seasonal_factor: float (1.0 = normal, 1.2 = musim ramai),
     *     base_operational_cost: float
     * }
     */
    public function calculateSimulation(array $params): array
    {
        $commodity = Commodity::find($params['commodity_id']);
        
        if (!$commodity) {
            return [
                'success' => false,
                'message' => 'Komoditas tidak ditemukan',
            ];
        }

        // 1. Hitung perubahan harga
        $priceChange = $params['simulated_price'] - $params['base_price'];
        $priceChangePercent = ($priceChange / $params['base_price']) * 100;

        // 2. Weighted Regression: Dampak terhadap biaya operasional
        $weightedImpact = $this->calculateWeightedRegression(
            $params['base_price'],
            $params['simulated_price'],
            $params['weight_in_basket'],
            $params['seasonal_factor'],
            $params['base_operational_cost']
        );

        // 3. Hitung impact score (0-100)
        $impactScore = $this->calculateImpactScore($priceChangePercent, $params['weight_in_basket']);

        // 4. Tentukan risk level
        $riskLevel = $this->determineRiskLevel($priceChangePercent, $impactScore);

        // 5. Buat rekomendasi
        $recommendations = $this->generateRecommendations(
            $commodity,
            $priceChangePercent,
            $impactScore,
            $riskLevel,
            $weightedImpact
        );

        return [
            'success' => true,
            'data' => [
                'commodity' => [
                    'id' => $commodity->id,
                    'name' => $commodity->name,
                    'unit' => $commodity->unit,
                ],
                'price_analysis' => [
                    'base_price' => (float) $params['base_price'],
                    'simulated_price' => (float) $params['simulated_price'],
                    'price_change' => (float) $priceChange,
                    'price_change_percent' => (float) round($priceChangePercent, 2),
                ],
                'weighted_impact' => [
                    'weight_in_basket' => (float) $params['weight_in_basket'],
                    'seasonal_factor' => (float) $params['seasonal_factor'],
                    'cost_increase_absolute' => (float) round($weightedImpact['cost_increase'], 2),
                    'cost_increase_percent' => (float) round($weightedImpact['cost_increase_percent'], 2),
                    'new_operational_cost' => (float) round($weightedImpact['new_operational_cost'], 2),
                ],
                'impact_score' => (int) $impactScore,
                'risk_level' => $riskLevel,
                'recommendations' => $recommendations,
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }

    /**
     * Weighted Regression Logic
     * Formula: Cost_Impact = (Price_Change / Base_Price) × Weight × Seasonal_Factor × Base_Cost
     */
    private function calculateWeightedRegression(
        float $basePrice,
        float $simulatedPrice,
        float $weight,
        float $seasonalFactor,
        float $baseOperationalCost
    ): array {
        // Perubahan harga dalam persentase
        $priceChangePercent = (($simulatedPrice - $basePrice) / $basePrice) * 100;

        // Hitung dampak biaya dengan weighted regression
        $costIncrease = ($priceChangePercent / 100) * $weight * $seasonalFactor * $baseOperationalCost;

        $newOperationalCost = $baseOperationalCost + $costIncrease;
        $costIncreasePercent = ($costIncrease / $baseOperationalCost) * 100;

        return [
            'cost_increase' => $costIncrease,
            'cost_increase_percent' => $costIncreasePercent,
            'new_operational_cost' => $newOperationalCost,
        ];
    }

    /**
     * Impact Score (0-100)
     * Faktor: perubahan harga + bobot komoditas dalam keranjang
     */
    private function calculateImpactScore(float $priceChangePercent, float $weight): int
    {
        // Normalisasi: |% change| × weight × 100
        $score = abs($priceChangePercent) * $weight * 100;
        
        // Cap di 100
        return (int) min($score, 100);
    }

    /**
     * Tentukan risk level
     */
    private function determineRiskLevel(float $priceChangePercent, int $impactScore): string
    {
        if ($impactScore >= 70 || abs($priceChangePercent) >= 30) {
            return 'CRITICAL';
        }

        if ($impactScore >= 40 || abs($priceChangePercent) >= 15) {
            return 'HIGH';
        }

        if ($impactScore >= 20 || abs($priceChangePercent) >= 5) {
            return 'MEDIUM';
        }

        return 'LOW';
    }

    /**
     * Generate rekomendasi berdasarkan analisis
     */
    private function generateRecommendations(
        Commodity $commodity,
        float $priceChange,
        int $impactScore,
        string $riskLevel,
        array $weightedImpact
    ): array {
        $recommendations = [];

        if ($riskLevel === 'CRITICAL') {
            $recommendations[] = [
                'type' => 'urgent',
                'text' => "Harga {$commodity->name} naik drastis ({$priceChange}%). Segera cari alternatif supplier atau kurangi penggunaan komoditas ini.",
            ];
            $recommendations[] = [
                'type' => 'mitigation',
                'text' => 'Naikkan harga jual produk atau cari substitusi komoditas yang lebih stabil.',
            ];
        }

        if ($riskLevel === 'HIGH') {
            $recommendations[] = [
                'type' => 'caution',
                'text' => "Tren harga {$commodity->name} menunjukkan kenaikan signifikan. Monitor terus dan pertimbangkan negosiasi dengan supplier.",
            ];
        }

        if ($priceChange < 0) {
            $recommendations[] = [
                'type' => 'opportunity',
                'text' => "Harga {$commodity->name} sedang turun ({$priceChange}%). Ini kesempatan bagus untuk stock up jika ada kapasitas penyimpanan.",
            ];
        }

        $recommendations[] = [
            'type' => 'analytics',
            'text' => "Dampak terhadap biaya operasional: Rp " . number_format($weightedImpact['cost_increase'], 0, ',', '.') . " ({$weightedImpact['cost_increase_percent']}%).",
        ];

        return $recommendations;
    }

    /**
     * Extract nilai dari cerita user (untuk AI Assistant)
     * Digunakan oleh AIAssistantService
     */
    public function extractParametersFromStory(array $aiExtraction): array
    {
        return [
            'commodity_id' => $aiExtraction['commodity_id'] ?? null,
            'price_change_percent' => $aiExtraction['price_change_percent'] ?? 0,
            'business_type' => $aiExtraction['business_type'] ?? null,
            'weight_in_basket' => $aiExtraction['weight_in_basket'] ?? 0.5,
            'seasonal_factor' => $aiExtraction['seasonal_factor'] ?? 1.0,
        ];
    }
}
