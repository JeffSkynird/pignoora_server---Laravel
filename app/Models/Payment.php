<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'credit_id',
        'amount',
        'pay_date',
        'interest',
        'balance',
        'status',
    ];
    public function credit()
    {
        return $this->belongsTo(Credit::class,'credit_id');
    }
}
