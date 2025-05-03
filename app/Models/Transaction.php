<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['total_items', 'total_price', 'payment', 'change'];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
