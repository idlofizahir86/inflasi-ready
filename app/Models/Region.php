<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $fillable = ['name', 'slug', 'status', 'color', 'textColor'];

    public function commodities(): HasMany
    {
        return $this->hasMany(Commodity::class);
    }
}