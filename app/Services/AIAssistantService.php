<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIAssistantService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        // Gunakan null coalescing (??) untuk memberikan default string kosong 
        // agar tidak terkena error "must not be accessed before initialization"
        $this->apiKey = config('services.gemini.key') ?? '';
        $this->model = config('services.gemini.model') ?? 'gemini-3-flash-preview';
        
        // Base URL untuk v1beta
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta";

        // Optional: Berikan log jika API Key kosong agar Anda mudah men-debug
        if (empty($this->apiKey)) {
            Log::warning('Gemini API Key is not set in configuration.');
        }
    }

    /**
     * Get HTTP client configuration untuk SSL verification
     * Di local environment (development/testing), gunakan verify=false
     * Di production, gunakan verify=true (default)
     */
    private function getHttpConfig(): array
    {
        return [
            'verify' => app()->isProduction() ? true : false,
        ];
    }

    private function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'x-goog-api-key' => $this->apiKey,
        ];
    }

    /**
     * Extract parameter dari narasi user menggunakan Gemini 3 Flash
     * Input: "Saya pedagang bakso, harga daging naik 15%"
     * Output: {commodity, price_change_percent, business_type, weight_in_basket, seasonal_factor}
     */
    public function extractSimulationParameters(string $userNarrative): array
    {
        $systemPrompt = $this->buildExtractionPrompt();

        try {
            // Format Payload Gemini (generateContent API)
            $payload = [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $systemPrompt . "\n\nUser Story: " . $userNarrative]
                        ]
                    ]
                ],
                "generationConfig" => [
                    "temperature" => 0.2,
                    "topP" => 0.8,
                    "topK" => 40
                ]
            ];

            $response = Http::withHeaders($this->getHeaders())
                ->withOptions(['verify' => false]) // Bypass SSL untuk localhost Bandung
                ->timeout(30)
                ->post("{$this->baseUrl}/models/{$this->model}:generateContent", $payload);

            if (!$response->successful()) {
                Log::error('Gemini Extract API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'Gagal terhubung dengan Gemini AI',
                    'data' => null,
                ];
            }

            // Parsing response Google
            $content = $response->json('candidates.0.content.parts.0.text');
            
            // Extract JSON dari markdown response (Gemini sering membungkus dengan ```json)
            $jsonMatch = preg_match('/\{.*\}/s', $content, $matches);
            
            if (!$jsonMatch) {
                Log::warning('Format JSON tidak ditemukan dalam respons Gemini', ['content' => $content]);
                return [
                    'success' => false,
                    'message' => 'Format respons AI tidak valid',
                    'data' => null,
                ];
            }

            $extracted = json_decode($matches[0], true);

            return [
                'success' => true,
                'message' => 'Parameter berhasil diekstraksi oleh Gemini',
                'data' => [
                    'commodity_id' => $extracted['commodity_id'] ?? null,
                    'price_change_percent' => (float) ($extracted['price_change_percent'] ?? 0),
                    'business_type' => $extracted['business_type'] ?? null,
                    'weight_in_basket' => (float) ($extracted['weight_in_basket'] ?? 0.5),
                    'seasonal_factor' => (float) ($extracted['seasonal_factor'] ?? 1.0),
                    'raw_response' => $content,
                ],
            ];

        } catch (\Exception $e) {
            Log::error('Gemini Extraction Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage(), 'data' => null];
        }
    }

    /**
     * System Prompt untuk Extraction
     */
    private function buildExtractionPrompt(): string
    {
        return <<<'PROMPT'
Anda adalah AI Assistant untuk platform monitoring harga komoditas ARTHADATA.
Tugas Anda adalah mengekstraksi parameter simulasi dari cerita/narasi user.

BAHASA: Gunakan hanya Bahasa Indonesia dalam semua respons.

OUTPUT FORMAT: Berikan respons dalam format JSON VALID yang dapat di-parse.

INSTRUKSI EKSTRAKSI:
1. Identifikasi COMMODITY_ID berdasarkan komoditas yang disebutkan:
   - Jika user sebut "daging" / "daging sapi" / "sapi" → commodity_id = 1
   - Jika user sebut "ayam" / "daging ayam" → commodity_id = 2
   - Jika user sebut "beras" / "padi" → commodity_id = 3
   - Jika user sebut "telur" / "telur ayam" → commodity_id = 4
   - Jika komoditas tidak jelas → commodity_id = null

2. PRICE_CHANGE_PERCENT: Cari angka perubahan harga (bisa positif/negatif)
   - "naik 15%" → +15
   - "turun 10%" → -10
   - Jika tidak jelas → 0

3. BUSINESS_TYPE: Jenis usaha user (pedagang bakso, peternak, warung makan, dll)
   - Ekstrak sebagai string deskriptif
   - Jika tidak jelas → null

4. WEIGHT_IN_BASKET: Perkiraan bobot komoditas dalam keranjang (0-1)
   - Jika "daging adalah input utama" → 0.8
   - Jika "salah satu dari banyak bahan" → 0.3
   - Default → 0.5

5. SEASONAL_FACTOR: Faktor musiman (1.0 = normal, >1.0 = musim ramai/panen)
   - Jika user sebut "menjelang Hari Raya" / "musim panen" → 1.3
   - Jika user sebut "musim sepi" → 0.8
   - Default → 1.0

CONTOH OUTPUT JSON:
{
  "commodity_id": 1,
  "price_change_percent": 15,
  "business_type": "pedagang bakso",
  "weight_in_basket": 0.7,
  "seasonal_factor": 1.0,
  "confidence": "high",
  "explanation_id": "Daging sapi adalah input utama, naik 15% menjelang normal market"
}

CATATAN PENTING:
- Respons HARUS JSON valid dan dapat di-parse dengan json_decode()
- Jika informasi tidak lengkap, berikan best guess dengan confidence rendah
- Jangan tambahkan teks di luar JSON
PROMPT;
    }

    /**
     * Chat endpoint untuk interactive conversation
     */
    public function chat(string $message, array $conversationContext = []): array
    {
        try {
            // Konversi context history ke format Gemini (user/model)
            $history = [];
            foreach ($conversationContext as $ctx) {
                $history[] = [
                    "role" => ($ctx['role'] === 'assistant') ? 'model' : 'user',
                    "parts" => [["text" => $ctx['content']]]
                ];
            }

            $payload = [
                "contents" => array_merge($history, [
                    [
                        "role" => "user",
                        "parts" => [
                            ["text" => "System Instruction: " . $this->buildChatSystemPrompt() . "\n\nUser Message: " . $message]
                        ]
                    ]
                ]),
                "generationConfig" => [
                    "temperature" => 0.7,
                    "maxOutputTokens" => 1000,
                ]
            ];

            $response = Http::withHeaders($this->getHeaders())
                ->withOptions(['verify' => false])
                ->timeout(30)
                ->post("{$this->baseUrl}/models/{$this->model}:generateContent", $payload);

            if (!$response->successful()) {
                Log::error('Gemini Chat API Error', ['body' => $response->body()]);
                return ['success' => false, 'message' => 'Gagal menghubungi Gemini AI'];
            }

            $reply = $response->json('candidates.0.content.parts.0.text');

            return [
                'success' => true,
                'message' => $reply,
            ];

        } catch (\Exception $e) {
            Log::error('Gemini Chat Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }

    /**
     * System Prompt untuk Chat
     */
    private function buildChatSystemPrompt(): string
    {
        return <<<'PROMPT'
Anda adalah AI Assistant dari platform ARTHADATA (Aras Harga, Satu Data).
Platform ini membantu UMKM dan Petani memonitor dan mensimulasikan dampak fluktuasi harga komoditas pangan.

KARAKTERISTIK:
- Sangat membantu dan ramah
- Gunakan Bahasa Indonesia yang jelas dan mudah dipahami
- Berikan saran praktis untuk mengatasi volatilitas harga
- Jelaskan analisis dengan istilah yang sederhana
- Jika user cerita masalah harga, tawarkan untuk membuat simulasi

JANGAN:
- Memberikan financial advice yang spesifik tanpa disclaimer
- Membuat janji tentang keuntungan pasti
- Merekomendasikan produk tertentu tanpa data

FOKUS TOPIK:
- Dampak fluktuasi harga komoditas
- Strategi mitigasi biaya operasional
- Simulasi harga dan implikasi bisnis
- Alternatif supplier dan sourcing
PROMPT;
    }
}
