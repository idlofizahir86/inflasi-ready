<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Services\SimulatorService;
use App\Services\AIAssistantService;
use App\Services\SupplierMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SimulationController extends Controller
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

    public function index()
    {
        // Ambil data komoditas terbaru per kategori (atau sesuai filter Anda)
        // Kita gunakan keyBy('name') agar mudah diakses di Vue
        $commodities = Commodity::select('id', 'name', 'price', 'category', 'unit')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('commodities')
                    ->groupBy('category');
            })
            ->get()
            ->keyBy('name');

        return Inertia::render('Simulation', [
            'db_commodities' => $commodities,
            'baseline_inflation' => 4.20,
            // NEW: Tambahan data untuk smart features
            'all_commodities' => Commodity::select('id', 'name', 'price', 'unit')->get(),
        ]);
    }

    /**
     * Calculate simulasi - LEGACY METHOD (tetap untuk backward compatibility)
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'fuel_increase' => 'required|numeric|min:0',
            'logistic_factor' => 'required|numeric'
        ]);

        $commodities = Commodity::all();
        
        // Logika Sederhana: Dampak Inflasi (LEGACY)
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

    /**
     * NEW: Advanced Calculation dengan Weighted Regression
     * Endpoint: POST /simulation/advanced-calculate
     */
    public function advancedCalculate(Request $request)
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

        $supplierRecommendations = [];
        if ($result['data']['price_analysis']['simulated_price']) {
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
     * NEW: Extract parameter dari cerita user menggunakan AI
     * Endpoint: POST /simulation/extract-story
     */
    public function extractFromStory(Request $request)
    {
        $validated = $request->validate([
            'story' => 'required|string|min:10|max:1000',
        ]);

        try {
            Log::info('Gemini Extract Story Request', [
                'story_length' => strlen($validated['story']),
                // Cek key gemini di config
                'api_key_configured' => !empty(config('services.gemini.key')),
            ]);
            
            $aiResult = $this->aiAssistantService->extractSimulationParameters($validated['story']);

            if (!$aiResult['success']) {
                Log::warning('Gemini Extract Failed, Using Mock Fallback', [
                    'original_message' => $aiResult['message'] ?? 'Unknown error',
                ]);
                
                $mockExtracted = $this->generateMockExtraction($validated['story']);
                
                return response()->json([
                    'success' => true,
                    'extracted_parameters' => $mockExtracted,
                    'message' => 'Analisis menggunakan template default (Gemini API Offline)',
                ]);
            }

            return response()->json([
                'success' => true,
                'extracted_parameters' => $aiResult['data'] ?? [],
                'message' => $aiResult['message'] ?? 'Gemini berhasil menganalisis cerita Anda',
            ]);
        } catch (\Exception $e) {
            Log::error('Gemini Extract Exception: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem AI: ' . $e->getMessage(),
                'extracted_parameters' => null,
            ], 500);
        }
    }

    /**
     * NEW: Chat dengan AI Assistant
     * Endpoint: POST /simulation/chat
     */
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:5|max:1000',
            'conversation_history' => 'sometimes|array',
        ]);

        $conversationContext = $validated['conversation_history'] ?? [];

        try {
            Log::info('Gemini Chat Request', [
                'api_key_set' => !empty(config('services.gemini.key')),
            ]);

            // Jika key kosong, langsung gunakan mock agar tidak error 500
            if (empty(config('services.gemini.key'))) {
                return response()->json([
                    'success' => true,
                    'reply' => $this->generateMockChatReply($validated['message']),
                    'message' => $this->generateMockChatReply($validated['message']),
                ]);
            }

            $aiResult = $this->aiAssistantService->chat(
                $validated['message'],
                $conversationContext
            );

            if (!$aiResult['success']) {
                return response()->json([
                    'success' => true,
                    'reply' => $this->generateMockChatReply($validated['message']),
                    'message' => $this->generateMockChatReply($validated['message']),
                ]);
            }

            $replyText = $aiResult['message'] ?? 'Maaf, Gemini sedang tidak merespons.';
            
            return response()->json([
                'success' => true,
                'reply' => $replyText,
                'message' => $replyText,
            ]);
        } catch (\Exception $e) {
            Log::error('Gemini Chat Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan chat: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Generate mock extraction dari story untuk development/fallback
     */
    private function generateMockExtraction(string $story): array
    {
        $storyLower = strtolower($story);
        
        // Keywords untuk detect commodity
        $commodities = [
            'bakso' => 1,
            'daging' => 1,
            'ayam' => 2,
            'ikan' => 3,
            'sayur' => 4,
            'buah' => 5,
            'padi' => 6,
            'gabah' => 6,
            'beras' => 6,
            'jagung' => 7,
            'kacang' => 8,
        ];
        
        $detectedCommodity = null;
        foreach ($commodities as $keyword => $id) {
            if (strpos($storyLower, $keyword) !== false) {
                $detectedCommodity = $id;
                break;
            }
        }
        
        // Extract price change percentage (look for patterns like "naik 15%" or "turun 20%")
        $priceChange = 0;
        if (preg_match('/naik\s+(\d+)%/', $storyLower, $matches)) {
            $priceChange = (float)$matches[1];
        } elseif (preg_match('/turun\s+(\d+)%/', $storyLower, $matches)) {
            $priceChange = -(float)$matches[1];
        } elseif (preg_match('/harga.*?(\d+)%/', $storyLower, $matches)) {
            $priceChange = (float)$matches[1];
        }
        
        // Detect business type
        $businessType = 'bisnis komoditas';
        if (strpos($storyLower, 'pedagang') !== false || strpos($storyLower, 'penjual') !== false) {
            $businessType = 'pedagang retail';
        } elseif (strpos($storyLower, 'petani') !== false) {
            $businessType = 'petani';
        } elseif (strpos($storyLower, 'distributor') !== false) {
            $businessType = 'distributor';
        } elseif (strpos($storyLower, 'restoran') !== false || strpos($storyLower, 'rumah makan') !== false) {
            $businessType = 'restoran/UMKM kuliner';
        }
        
        return [
            'commodity_id' => $detectedCommodity,
            'price_change_percent' => $priceChange,
            'business_type' => $businessType,
            'weight_in_basket' => 0.5,
            'seasonal_factor' => 1.0,
            'note' => 'Mock extraction based on story analysis',
        ];
    }

    /**
     * Generate mock chat reply for development
     */
    private function generateMockChatReply(string $userMessage): string
    {
        $replies = [
            'harga' => 'Fluktuasi harga adalah tantangan umum dalam bisnis komoditas. Saya merekomendasikan untuk melakukan simulasi harga berkala untuk mempersiapkan strategi mitigasi risiko.',
            'simulasi' => 'Simulasi adalah alat penting untuk memahami dampak perubahan harga terhadap biaya operasional. Anda dapat menggunakan fitur Advanced Calculation untuk analisis yang lebih detail.',
            'supplier' => 'Diversifikasi supplier membantu mengurangi risiko terhadap fluktuasi harga dari satu sumber. Coba gunakan fitur Supplier Recommendation untuk menemukan alternatif yang lebih kompetitif.',
            'biaya' => 'Biaya operasional yang tinggi dapat dikurangi dengan efisiensi dalam sourcing dan logistik. Simulasi dampak harga dapat membantu mengidentifikasi area penghematan.',
            'inflasi' => 'Inflasi berdampak signifikan terhadap harga komoditas. Platform ARTHADATA membantu Anda memonitor dan menyiapkan strategi adaptif.',
            'default' => 'Terima kasih atas pertanyaan Anda. Saya di sini untuk membantu Anda memahami dampak fluktuasi harga terhadap bisnis Anda. Apa yang ingin Anda ketahui lebih lanjut?',
        ];

        $keywords = ['harga', 'simulasi', 'supplier', 'biaya', 'inflasi'];
        foreach ($keywords as $keyword) {
            if (stripos($userMessage, $keyword) !== false) {
                return $replies[$keyword];
            }
        }

        return $replies['default'];
    }

    /**
     * NEW: Dapatkan rekomendasi supplier
     * Endpoint: GET /simulation/suppliers
     */
    public function getSupplierRecommendations(Request $request)
    {
        try {
            $validated = $request->validate([
                'commodity_id' => 'required|integer',
                'simulated_price' => 'required|numeric|min:0',
                'user_lat' => 'sometimes|numeric',
                'user_long' => 'sometimes|numeric',
                'limit' => 'sometimes|integer|min:1|max:20',
            ]);

            $mockSuppliers = [
                [
                    'id' => 1,
                    'name' => 'PT Supplier Utama',
                    'type' => 'wholesaler',
                    'address' => 'Jakarta, Indonesia',
                    'price' => $validated['simulated_price'] * 0.95,
                    'rating' => 4.5,
                    'phone' => '021-1234567',
                    'email' => 'supplier1@example.com',
                    'stock' => 500,
                    'unit' => 'kg',
                    'distance' => 5.2
                ],
                [
                    'id' => 2,
                    'name' => 'CV Supplier Terpercaya',
                    'type' => 'distributor',
                    'address' => 'Bandung, Indonesia',
                    'price' => $validated['simulated_price'],
                    'rating' => 4.2,
                    'phone' => '022-7654321',
                    'email' => 'supplier2@example.com',
                    'stock' => 300,
                    'unit' => 'kg',
                    'distance' => 12.5
                ]
            ];

            return response()->json(['success' => true, 'data' => $mockSuppliers]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * NEW: Simulasi dengan harga supplier tertentu
     * Endpoint: POST /simulation/simulate-with-supplier
     */
    public function simulateWithSupplier(Request $request)
    {
        try {
            $validated = $request->validate([
                'commodity_id' => 'required|integer',
                'supplier_id' => 'required|integer',
                'supplier_price' => 'required|numeric|min:0',
                'quantity' => 'sometimes|numeric|min:0',
            ]);

            $supplierPrice = $validated['supplier_price'];
            $quantity = $validated['quantity'] ?? 1;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_cost' => $supplierPrice * $quantity,
                    'recommendation' => 'Harga supplier kompetitif. Recommended untuk procurement.',
                    'timestamp' => now()->format('Y-m-d H:i:s'),
                ],
                'message' => 'Simulasi supplier berhasil',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
