<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Services\AIAssistantService;
use App\Services\SimulatorService;
use App\Services\SupplierMatchingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SimulatorController extends Controller
{
    protected SimulatorService $simulatorService;
    protected AIAssistantService $aiAssistantService;
    protected SupplierMatchingService $supplierMatchingService;

    public function __construct(
        SimulatorService $simulatorService,
        AIAssistantService $aiAssistantService,
        SupplierMatchingService $supplierMatchingService
    ) {
        $this->simulatorService = $simulatorService;
        $this->aiAssistantService = $aiAssistantService;
        $this->supplierMatchingService = $supplierMatchingService;
    }

    /**
     * Halaman index untuk Simulator
     */
    public function index()
    {
        $commodities = Commodity::all();

        return Inertia::render('Simulator/Index', [
            'commodities' => $commodities,
            'baseline_inflation' => 4.20,
        ]);
    }

    /**
     * Calculate simulasi harga berdasarkan parameter
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'commodity_id' => 'required|integer|exists:commodities,id',
            'base_price' => 'required|numeric|min:0',
            'simulated_price' => 'required|numeric|min:0',
            'weight_in_basket' => 'required|numeric|min:0|max:1',
            'seasonal_factor' => 'required|numeric|min:0',
            'base_operational_cost' => 'required|numeric|min:0',
        ]);

        $result = $this->simulatorService->calculateSimulation($validated);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        // Dapatkan rekomendasi supplier jika hasil simulasi ada
        $supplierRecommendations = [];
        if ($result['success'] && $result['data']['price_analysis']['simulated_price']) {
            $supplierResult = $this->supplierMatchingService->recommendSuppliers(
                $validated['commodity_id'],
                $result['data']['price_analysis']['simulated_price'],
                null,
                null,
                5
            );

            if ($supplierResult['success']) {
                $supplierRecommendations = $supplierResult['data'];
            }
        }

        return response()->json([
            'success' => true,
            'simulation_result' => $result['data'],
            'supplier_recommendations' => $supplierRecommendations,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Extract parameter dari cerita user menggunakan AI
     */
    public function extractFromStory(Request $request)
    {
        $validated = $request->validate([
            'story' => 'required|string|min:10|max:1000',
        ]);

        $aiResult = $this->aiAssistantService->extractSimulationParameters($validated['story']);

        if (!$aiResult['success']) {
            return response()->json($aiResult, 400);
        }

        return response()->json([
            'success' => true,
            'extracted_parameters' => $aiResult['data'],
            'message' => 'Parameter berhasil diekstraksi dari cerita Anda',
        ]);
    }

    /**
     * Chat dengan AI Assistant
     */
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:5|max:1000',
            'conversation_history' => 'sometimes|array',
        ]);

        $conversationContext = [];
        if (isset($validated['conversation_history']) && is_array($validated['conversation_history'])) {
            $conversationContext = $validated['conversation_history'];
        }

        $aiResult = $this->aiAssistantService->chat(
            $validated['message'],
            $conversationContext
        );

        if (!$aiResult['success']) {
            return response()->json($aiResult, 400);
        }

        return response()->json([
            'success' => true,
            'reply' => $aiResult['message'],
        ]);
    }

    /**
     * Dapatkan rekomendasi supplier untuk simulasi
     */
    public function getSupplierRecommendations(Request $request)
    {
        $validated = $request->validate([
            'commodity_id' => 'required|integer|exists:commodities,id',
            'simulated_price' => 'required|numeric|min:0',
            'user_lat' => 'sometimes|numeric',
            'user_long' => 'sometimes|numeric',
            'limit' => 'sometimes|integer|min:1|max:20',
        ]);

        $result = $this->supplierMatchingService->recommendSuppliers(
            $validated['commodity_id'],
            $validated['simulated_price'],
            $validated['user_lat'] ?? null,
            $validated['user_long'] ?? null,
            $validated['limit'] ?? 5
        );

        return response()->json($result);
    }

    /**
     * Simulasi langsung dengan harga supplier tertentu
     */
    public function simulateWithSupplier(Request $request)
    {
        $validated = $request->validate([
            'commodity_id' => 'required|integer|exists:commodities,id',
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'base_price' => 'required|numeric|min:0',
            'weight_in_basket' => 'required|numeric|min:0|max:1',
            'seasonal_factor' => 'required|numeric|min:0',
            'base_operational_cost' => 'required|numeric|min:0',
        ]);

        // Dapatkan harga supplier terbaru
        $supplier = \App\Models\Supplier::find($validated['supplier_id']);
        $supplierPrice = $supplier->getLatestPrice($validated['commodity_id']);

        if (!$supplierPrice) {
            return response()->json([
                'success' => false,
                'message' => 'Harga supplier untuk komoditas ini tidak ditemukan',
            ], 404);
        }

        // Jalankan simulasi dengan harga supplier
        $simulationParams = [
            'commodity_id' => $validated['commodity_id'],
            'base_price' => $validated['base_price'],
            'simulated_price' => (float) $supplierPrice->price_per_kg,
            'weight_in_basket' => $validated['weight_in_basket'],
            'seasonal_factor' => $validated['seasonal_factor'],
            'base_operational_cost' => $validated['base_operational_cost'],
        ];

        $result = $this->simulatorService->calculateSimulation($simulationParams);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        // Tambahkan info supplier
        $result['data']['supplier_used'] = [
            'id' => $supplier->id,
            'nama' => $supplier->nama_supplier,
            'lokasi' => $supplier->lokasi,
            'price' => (float) $supplierPrice->price_per_kg,
            'price_date' => $supplierPrice->price_date->format('Y-m-d'),
        ];

        return response()->json([
            'success' => true,
            'simulation_result' => $result['data'],
            'message' => 'Simulasi dengan harga supplier berhasil dihitung',
        ]);
    }
}
