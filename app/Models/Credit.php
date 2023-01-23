<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    use HasFactory;
    protected $fillable = [
        'pawn_id',
        'asesor_id',
        'amount',
        'term',
        'interest',
        'pay_type',
        'start_date',
        'status',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
