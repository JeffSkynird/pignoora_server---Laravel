<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PawnImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'url',
        'pawn_id',
        'delete_hash'
    ];
}
