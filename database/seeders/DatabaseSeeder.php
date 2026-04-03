<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Commodity;
use App\Models\Supplier;
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
        User::create([
            'name' => 'Demo Test',
            'email' => 'akun-test@pidi.id',
            'password' => Hash::make('123456'),
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Developer Akhir Zaman',
            'email' => 'developerakhirzaman@indonesia',
            'password' => Hash::make('developerakhirzaman'),
            'email_verified_at' => now(),
        ]);

        // 1. Jalankan RegionSeeder untuk membuat semua 38 provinces
        $this->call(RegionSeeder::class);
        
        // 2. Jalankan CommoditySeeder untuk membuat commodities di semua regions
        $this->call(CommoditySeeder::class);
        
        // 3. Jalankan MarketSeeder untuk membuat markets di semua regions
        $this->call(MarketSeeder::class);

        $this->call(SupplierSeeder::class);
    }

    private function seedCommoditiesForRegion($region)
    {
        // This method is no longer used - moved to CommoditySeeder
    }
}