<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'branch_id',
        'table_id',
        'user_id',
        'total_amount',
        'status',
        'order_type',
        'notes',
        'discount',
        'payment_method',
        'amount_tendered',
        'change_amount',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'discount' => 'float',
        'amount_tendered' => 'float',
        'change_amount' => 'float',
        'paid_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
