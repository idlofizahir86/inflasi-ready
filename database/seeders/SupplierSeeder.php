<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\SupplierPrice;
use App\Models\Commodity;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Data supplier Bandung area (Bojongsoang)
        $suppliersData = [
            [
                'nama_supplier' => 'PT Mitra Pangan Bandung',
                'lokasi' => 'Bojongsoang, Bandung, Jl. Riau No.123',
                'kontak' => '+62-274-401-2345',
                'coordinate_lat' => -6.9167,
                'coordinate_long' => 107.6667,
                'is_verified' => true,
                'price_update_frequency' => 'daily',
                'rating' => 4.5,
                'min_order_kg' => 5,
            ],
            [
                'nama_supplier' => 'Koperasi Tani Sejahtera',
                'lokasi' => 'Bojongsoang, Bandung, Jl. Subang Km 10',
                'kontak' => '+62-271-505-6789',
                'coordinate_lat' => -6.9180,
                'coordinate_long' => 107.6650,
                'is_verified' => true,
                'price_update_frequency' => 'weekly',
                'rating' => 4.2,
                'min_order_kg' => 10,
            ],
            [
                'nama_supplier' => 'CV Sumber Alam Lestari',
                'lokasi' => 'Bojongsoang, Bandung, Jl. Terusan Pelangi',
                'kontak' => '+62-272-555-7890',
                'coordinate_lat' => -6.9200,
                'coordinate_long' => 107.6680,
                'is_verified' => true,
                'price_update_frequency' => 'daily',
                'rating' => 4.7,
                'min_order_kg' => 3,
            ],
            [
                'nama_supplier' => 'UD Makmur Bersama',
                'lokasi' => 'Bandung Timur, Jl. Ciledug No.456',
                'kontak' => '+62-273-666-8901',
                'coordinate_lat' => -6.9100,
                'coordinate_long' => 107.6800,
                'is_verified' => false,
                'price_update_frequency' => 'weekly',
                'rating' => 3.8,
                'min_order_kg' => 20,
            ],
            [
                'nama_supplier' => 'Toko Grosir Sentosa',
                'lokasi' => 'Bandung Pusat, Jl. Braga No.789',
                'kontak' => '+62-274-777-9012',
                'coordinate_lat' => -6.9215,
                'coordinate_long' => 107.6110,
                'is_verified' => true,
                'price_update_frequency' => 'daily',
                'rating' => 4.4,
                'min_order_kg' => 5,
            ],
        ];

        // Create suppliers
        $suppliers = [];
        foreach ($suppliersData as $data) {
            $suppliers[] = Supplier::create($data);
        }

        // Get commodities
        $commodities = Commodity::all();

        if ($commodities->isEmpty()) {
            $this->command->warn('Tidak ada komoditas. Jalankan CommoditySeeder terlebih dahulu.');
            return;
        }

        // Add prices untuk setiap supplier dan komoditas
        // Harga berkisar sekitar harga komoditas ± 10%
        foreach ($suppliers as $supplier) {
            foreach ($commodities as $commodity) {
                $basePrice = $commodity->price;
                
                // Harga supplier sedikit berbeda dari base price
                $variasi = ($supplier->id % 3 - 1) * 0.05; // -5%, 0%, +5%
                $supplierPrice = $basePrice * (1 + $variasi);

                SupplierPrice::create([
                    'supplier_id' => $supplier->id,
                    'commodity_id' => $commodity->id,
                    'price_per_kg' => round($supplierPrice, 2),
                    'price_date' => Carbon::now()->subDays(rand(0, 7)),
                    'notes' => "Harga terbaru dari {$supplier->nama_supplier}",
                ]);
            }
        }

        $this->command->info('✅ Supplier dan supplier prices berhasil dibuat!');
    }
}
