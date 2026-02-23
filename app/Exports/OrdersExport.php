<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;

class OrdersExport implements FromCollection
{
    public function collection()
    {
        return Order::where('status','completed')
            ->select('id','total_amount','status','created_at')
            ->get();
    }
}