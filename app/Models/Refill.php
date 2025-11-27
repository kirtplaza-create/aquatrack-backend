<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refill extends Model
{
    protected $fillable = ['sale_id', 'type', 'gallons_dispensed', 'completed_at'];
    public function sale() { return $this->belongsTo(Sale::class); }
}
