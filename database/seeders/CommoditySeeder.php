<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Commodity;
use App\Models\Region;

class CommoditySeeder extends Seeder
{
    public function run()
    {
        // Ambil semua ID region yang tersedia
        $regionIds = Region::pluck('id')->toArray();

        $categories = [
            'Karbohidrat' => ['Beras Medium', 'Beras Premium', 'Beras IR64', 'Beras Merah', 'Beras Ketan', 'Tepung Terigu', 'Tepung Tapioka', 'Jagung Pipilan', 'Kentang Dieng', 'Ubi Jalar'],
            'Protein' => ['Daging Ayam Broiler', 'Daging Ayam Kampung', 'Daging Sapi Paha Belakang', 'Daging Sapi Has Luar', 'Telur Ayam Ras', 'Telur Bebek', 'Ikan Kembung', 'Ikan Bandeng', 'Ikan Tongkol', 'Daging Kambing'],
            'Hortikultura' => ['Cabai Merah Keriting', 'Cabai Rawit Merah', 'Cabai Hijau Besar', 'Bawang Merah Lokal', 'Bawang Putih Honan', 'Tomat Merah', 'Wortel Lokal', 'Kol Bulat', 'Sawi Hijau', 'Kangkung'],
            'Minyak & Gula' => ['Minyak Goreng Curah', 'Minyak Goreng Kemasan', 'Gula Pasir Lokal', 'Gula Kristal Putih', 'Garam Halus', 'Mentega Putih'],
            'Bumbu & Rempah' => ['Merica Putih', 'Ketumbar Bubuk', 'Jahe Gajah', 'Kunyit Tua', 'Lengkuas', 'Kayu Manis', 'Cengkeh Kering']
        ];

        $trends = ['+1.2%', '-0.8%', '+2.4%', '-3.1%', '+5.0%', '+12.1%', 'STABLE', '-0.5%', '+8.4%'];

        foreach ($categories as $cat => $items) {
            foreach ($items as $item) {
                for ($i = 1; $i <= 3; $i++) {
                    $grade = ($i == 1) ? "" : " Grade $i";
                    $price = rand(10000, 150000);
                    
                    Commodity::create([
                        'region_id' => $regionIds[array_rand($regionIds)], // Relasi ke Region
                        'name' => $item . $grade,
                        'category' => $cat,
                        'price' => $price, // Simpan sebagai integer agar mudah diolah di frontend
                        'trend' => $trends[array_rand($trends)],
                        'status' => $price > 100000 ? 'critical' : ($price > 50000 ? 'warning' : 'safe'),
                        'unit' => 'kg', // Sesuai model Anda
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}