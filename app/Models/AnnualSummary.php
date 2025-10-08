<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'total_revenue',
        'total_expense',
        'net_profit',
        'total_orders',
        'total_customers'
    ];
}
