<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use Illuminate\Support\Str;

class RegionSeeder extends Seeder
{
    public function run()
    {
        $provinces = [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau', 'Jambi', 'Sumatera Selatan', 'Bangka Belitung', 'Bengkulu', 'Lampung',
            'DKI Jakarta', 'Jawa Barat', 'Banten', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur',
            'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur',
            'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
            'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat',
            'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat', 'Papua Tengah', 'Papua Pegunungan', 'Papua Selatan', 'Papua Barat Daya'
        ];

        $statusOptions = [
            ['status' => 'CRITICAL', 'color' => 'bg-error', 'textColor' => 'text-error'],
            ['status' => 'MODERATE', 'color' => 'bg-orange-400', 'textColor' => 'text-orange-400'],
            ['status' => 'STABLE', 'color' => 'bg-primary', 'textColor' => 'text-primary'],
        ];

        foreach ($provinces as $province) {
            $pick = $statusOptions[array_rand($statusOptions)];
            
            Region::create([
                'name' => $province,
                'slug' => Str::slug($province), // Tambahkan slug agar tidak error
                'status' => $pick['status'],
                'color' => $pick['color'],
                'textColor' => $pick['textColor'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}