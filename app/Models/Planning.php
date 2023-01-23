<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'exam_id',
        'value',
        'is_complete',
        'user_id'
    ];
    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
    public function exam()
    {
        return $this->belongsTo(Exam::class,'exam_id');
    }
}
