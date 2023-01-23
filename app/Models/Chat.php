<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $fillable = [
        'message',
        'pawn_id',
        'user_id',
        'admin_id',
        'is_complete',
        'type',
        'value',
        'is_admin',
        'created_at'
    ];
  
}
