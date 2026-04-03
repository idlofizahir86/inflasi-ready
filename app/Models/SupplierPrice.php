<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPrice extends Model
{
    protected $fillable = [
        'supplier_id',
        'commodity_id',
        'price_per_kg',
        'price_date',
        'notes',
    ];

    protected $casts = [
        'price_per_kg' => 'decimal:2',
        'price_date' => 'datetime',
    ];

    /**
     * Relasi ke Supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi ke Commodity
     */
    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }
}
