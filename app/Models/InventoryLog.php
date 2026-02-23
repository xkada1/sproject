<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $fillable = [
        'product_id','user_id','action','qty',
        'stock_before','stock_after',
        'reserved_before','reserved_after',
        'reference'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}