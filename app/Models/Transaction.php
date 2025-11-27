<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['sale_id', 'amount', 'status'];
    public function sale() { return $this->belongsTo(Sale::class); }
}
