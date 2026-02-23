<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'supplier_id',
        'category_id',
        'name',
        'description',
        'image',

        // pricing
        'cost_price',
        'price',

        // inventory (NULL stock = unlimited)
        'stock',
        'reserved_stock',
        'low_stock_alert',
    ];

    protected $casts = [
        'cost_price' => 'float',
        'price' => 'float',
        'stock' => 'integer',
        'reserved_stock' => 'integer',
        'low_stock_alert' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getAvailableStockAttribute(): ?int
    {
        if (is_null($this->stock)) return null;
        return max(0, (int) $this->stock - (int) $this->reserved_stock);
    }
}
