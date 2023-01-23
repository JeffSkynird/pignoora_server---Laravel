<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'pawn_id',
        'driver_id',
        'condition',
        'observation',
    ];
    public function pawn()
    {
        return $this->belongsTo(Pawn::class,'pawn_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'driver_id');
    }
}
