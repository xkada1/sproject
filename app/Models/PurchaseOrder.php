<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'branch_id',
        'supplier_id',
        'po_number',
        'status',
        'notes',
        'ordered_at',
        'received_at',
        'total_amount',
    ];

    protected $casts = [
        'ordered_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
