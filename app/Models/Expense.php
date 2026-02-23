<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'branch_id',
        'user_id',
        'category',
        'description',
        'amount',
        'spent_at',
    ];

    protected $casts = [
        'spent_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
