<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'name',              // customer name column in your sales table
        'purpose',
        'gallons',
        'price_per_gallon',
        'total_amount',
        'status',
    ];
}
