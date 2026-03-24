<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommoditySyncController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'category' => 'required|string',
            'price'    => 'required|numeric|min:0',
            'status'   => 'required|in:stable,rising,falling',
        ]);

        try {
            // 2. Sinkronisasi Data
            // Jika name & category cocok, update price & status. Jika tidak, create.
            $commodity = Commodity::updateOrCreate(
                ['name' => $data['name'], 'category' => $data['category']],
                [
                    'price' => $data['price'],
                    'status' => $data['status']
                ]
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Data synchronized for: ' . $commodity->name,
                'data'    => $commodity
            ], 200);

        } catch (\Exception $e) {
            Log::error('Sync Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Sync failed'], 500);
        }
    }
}