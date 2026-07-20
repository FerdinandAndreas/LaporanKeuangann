<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'current_stock',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
    ];

    /**
     * Cek apakah stok termasuk kategori menipis (< 10 unit).
     */
    public function isLowStock(): bool
    {
        return (float) $this->current_stock < 10;
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
