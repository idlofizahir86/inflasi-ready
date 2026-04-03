<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_supplier',
        'lokasi',
        'kontak',
        'coordinate_lat',
        'coordinate_long',
        'is_verified',
        'price_update_frequency',
        'rating',
        'min_order_kg',
    ];

    protected $casts = [
        'coordinate_lat' => 'decimal:8',
        'coordinate_long' => 'decimal:8',
        'is_verified' => 'boolean',
        'rating' => 'decimal:2',
    ];

    /**
     * Relasi ke Supplier Prices
     */
    public function prices(): HasMany
    {
        return $this->hasMany(SupplierPrice::class);
    }

    /**
     * Dapatkan harga terbaru untuk komoditas tertentu
     */
    public function getLatestPrice(int $commodityId): ?SupplierPrice
    {
        return $this->prices()
            ->where('commodity_id', $commodityId)
            ->orderBy('price_date', 'desc')
            ->first();
    }

    /**
     * Hitung jarak dari koordinat (Haversine formula)
     */
    public function calculateDistance(float $userLat, float $userLong): ?float
    {
        if (!$this->coordinate_lat || !$this->coordinate_long) {
            return null;
        }

        $earthRadiusKm = 6371;
        $latDelta = deg2rad($userLat - $this->coordinate_lat);
        $longDelta = deg2rad($userLong - $this->coordinate_long);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($this->coordinate_lat)) * cos(deg2rad($userLat)) *
            sin($longDelta / 2) * sin($longDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c; // dalam KM
    }
}
