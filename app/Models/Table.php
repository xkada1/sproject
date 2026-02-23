<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'capacity',
        'status',
        'qr_token',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Customer-facing menu URL used by the QR module.
     */
    public function getMenuUrlAttribute(): string
    {
        return route('menu.show', $this->qr_token);
    }
}
