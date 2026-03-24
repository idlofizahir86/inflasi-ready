<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Commodity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Buat Demo User (akun-demo@pidi.id)
        User::create([
            'name' => 'Demo User',
            'email' => 'akun-demo@pidi.id',
            'password' => Hash::make('123456'),
            'email_verified_at' => now(),
        ]);

        // 1. Data Wilayah (Fokus Bandung Raya & Jawa Barat)
        $regions = [
            ['name' => 'Kota Bandung'],
            ['name' => 'Kabupaten Bandung'],
            ['name' => 'Kota Cimahi'],
            ['name' => 'Kabupaten Bandung Barat'],
            ['name' => 'Sumedang'],
            ['name' => 'Garut'],
        ];

        foreach ($regions as $reg) {
            $region = Region::create([
                'name' => $reg['name'],
                'slug' => Str::slug($reg['name']),
            ]);

            // 2. Data Komoditas untuk setiap Wilayah
            // Kita buat variasi harga sedikit berbeda antar wilayah agar grafik nanti terlihat dinamis
            $this->seedCommoditiesForRegion($region);
        }
    }

    private function seedCommoditiesForRegion($region)
    {
        $baseCommodities = [
            [
                'name' => 'Beras Medium',
                'category' => 'Pangan Pokok',
                'price' => 14500,
                'unit' => 'kg',
                'status' => 'stable'
            ],
            [
                'name' => 'Cabai Merah Keriting',
                'category' => 'Hortikultura',
                'price' => 45000,
                'unit' => 'kg',
                'status' => 'warning'
            ],
            [
                'name' => 'Daging Ayam Ras',
                'category' => 'Protein Hewani',
                'price' => 38000,
                'unit' => 'kg',
                'status' => 'safe'
            ],
            [
                'name' => 'Telur Ayam Ras',
                'category' => 'Protein Hewani',
                'price' => 29500,
                'unit' => 'kg',
                'status' => 'stable'
            ],
            [
                'name' => 'Minyak Goreng Kita',
                'category' => 'Minyak & Lemak',
                'price' => 15700,
                'unit' => 'liter',
                'status' => 'stable'
            ],
            [
                'name' => 'Gula Pasir Kristal',
                'category' => 'Pemanis',
                'price' => 17500,
                'unit' => 'kg',
                'status' => 'warning'
            ],
            [
                'name' => 'Bawang Merah',
                'category' => 'Hortikultura',
                'price' => 32000,
                'unit' => 'kg',
                'status' => 'safe'
            ],
        ];

        foreach ($baseCommodities as $item) {
            // Memberikan variasi harga acak +/- 5% antar wilayah agar data tidak identik
            $variation = rand(-500, 1000);
            
            Commodity::create([
                'region_id' => $region->id,
                'name' => $item['name'],
                'category' => $item['category'],
                'price' => $item['price'] + $variation,
                'unit' => $item['unit'],
                'status' => $item['status'],
            ]);
        }
    }
}