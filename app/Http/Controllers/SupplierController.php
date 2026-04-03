<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    /**
     * Menampilkan halaman Supplier
     * GET /supplier
     */
    public function index()
    {
        // Fetch commodities untuk dropdown
        $commodities = Commodity::select('id', 'name', 'unit')->get();

        return Inertia::render('Supplier', [
            'commodities' => $commodities,
        ]);
    }

    /**
     * API endpoint untuk get supplier recommendations
     * GET /api/suppliers/recommendations
     */
    public function getRecommendations(Request $request)
    {
        try {
            $validated = $request->validate([
                'commodity_id' => 'required|integer|exists:commodities,id',
                'price' => 'required|numeric|min:0',
                'user_lat' => 'sometimes|numeric',
                'user_long' => 'sometimes|numeric',
                'limit' => 'sometimes|integer|min:1|max:20',
                'filter_type' => 'sometimes|string|in:all,wholesaler,retailer,distributor,producer',
            ]);

            $filter_type = $validated['filter_type'] ?? 'all';
            $limit = $validated['limit'] ?? 5;

            // Get suppliers from SupplierMatchingService
            $service = app(\App\Services\SupplierMatchingService::class);
            
            $suppliers = $service->recommendSuppliers(
                $validated['commodity_id'],
                $validated['price'],
                $validated['user_lat'] ?? null,
                $validated['user_long'] ?? null,
                $limit
            );

            // Filter by type if specified
            if ($filter_type !== 'all') {
                $suppliers['data'] = collect($suppliers['data'])
                    ->filter(fn($supplier) => ($supplier['type'] ?? 'wholesaler') === $filter_type)
                    ->values()
                    ->all();
            }

            return response()->json($suppliers);
        } catch (\Exception $e) {
            Log::error('Supplier Recommendations Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error mendapatkan supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simulate with selected supplier
     * POST /api/suppliers/simulate
     */
    public function simulate(Request $request)
    {
        try {
            $validated = $request->validate([
                'commodity_id' => 'required|integer|exists:commodities,id',
                'supplier_id' => 'required|integer',
                'supplier_price' => 'required|numeric|min:0',
                'quantity' => 'sometimes|numeric|min:1',
            ]);

            // Here you can process the simulation
            // For now, return success response
            return response()->json([
                'success' => true,
                'message' => 'Simulasi dengan supplier berhasil disimpan',
                'data' => [
                    'commodity_id' => $validated['commodity_id'],
                    'supplier_id' => $validated['supplier_id'],
                    'supplier_price' => $validated['supplier_price'],
                    'quantity' => $validated['quantity'] ?? 1,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Supplier Simulation Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error simulasi supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supplier details
     * GET /api/suppliers/{id}
     */
    public function show($id)
    {
        try {
            // Return mock supplier data
            $supplier = [
                'id' => $id,
                'name' => 'Supplier ' . $id,
                'type' => 'wholesaler',
                'address' => '123 Main Street',
                'city' => 'Jakarta',
                'phone' => '+62-123-456-7890',
                'email' => 'supplier@example.com',
                'price' => 5000,
                'rating' => 4.5,
                'stock' => 1000,
                'unit' => 'kg',
            ];

            return response()->json([
                'success' => true,
                'data' => $supplier
            ]);
        } catch (\Exception $e) {
            Log::error('Supplier Detail Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error mendapatkan detail supplier'
            ], 500);
        }
    }

    /**
     * Get all suppliers
     * GET /api/suppliers
     */
    public function list(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);

            // Return mock suppliers list
            $suppliers = collect(range(1, 50))->map(function ($id) {
                return [
                    'id' => $id,
                    'name' => 'Supplier ' . $id,
                    'type' => ['wholesaler', 'retailer', 'distributor', 'producer'][rand(0, 3)],
                    'address' => 'Jalan ' . $id,
                    'city' => 'Jakarta',
                    'phone' => '+62-123-456-' . str_pad($id, 4, '0', STR_PAD_LEFT),
                    'email' => 'supplier' . $id . '@example.com',
                    'price' => 4000 + ($id * 100),
                    'rating' => 3.5 + (($id % 20) / 10),
                    'stock' => 500 + ($id * 10),
                    'unit' => 'kg',
                ];
            });

            $paginated = $suppliers
                ->slice(($page - 1) * $perPage, $perPage)
                ->values();

            return response()->json([
                'success' => true,
                'data' => $paginated,
                'pagination' => [
                    'total' => 50,
                    'per_page' => $perPage,
                    'page' => $page,
                    'last_page' => ceil(50 / $perPage),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Supplier List Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error mendapatkan daftar supplier'
            ], 500);
        }
    }
}
