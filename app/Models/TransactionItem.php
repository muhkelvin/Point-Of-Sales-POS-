<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = ['transaction_id', 'bread_id', 'quantity', 'price','sort'];

    public function bread()
    {
        return $this->belongsTo(Bread::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
