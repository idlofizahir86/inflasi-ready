<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commodity extends Model
{
    // Tambahkan region_id di sini
    protected $fillable = ['region_id', 'name', 'category', 'price', 'trend', 'status', 'unit'];

    // Relasi ke Region
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}