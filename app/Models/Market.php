<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Market extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id', 
        'name', 
        'latest_price', 
        'trend'
    ];

    /**
     * Relasi ke Region (Satu pasar berada di satu wilayah)
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}