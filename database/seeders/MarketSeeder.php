<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Market;
use Illuminate\Database\Seeder;

class MarketSeeder extends Seeder
{
    public function run(): void
    {
        $regions = Region::all();
        
        if ($regions->isEmpty()) return;

        for ($i = 1; $i <= 200; $i++) {
            Market::create([
                'region_id' => $regions->random()->id,
                'name' => 'Pasar ' . fake()->unique()->city(),
                'latest_price' => fake()->numberBetween(10000, 60000),
                'updated_at' => now()->subHours(rand(1, 24)),
            ]);
        }
    }
}
