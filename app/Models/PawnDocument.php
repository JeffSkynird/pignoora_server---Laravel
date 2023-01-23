<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PawnDocument extends Model
{
    use HasFactory;
    protected $fillable = [
        'url',
        'description',
        'pawn_id',
    ];
}
