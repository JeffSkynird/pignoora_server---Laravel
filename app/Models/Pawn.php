<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pawn extends Model
{
    use HasFactory;
    protected $fillable = [
        'features',
        'user_id',
        'value',
        'is_acepted',
        'asesor_id',
        'pawn_type',
        'brand',
        'model',
        'location',
        'status','driver_id','observation'
    ];

    public function model()
    {
        return $this->belongsTo(ModelProduct::class,'model_id');
    }
    public function getCreatedAtAttribute($date)
    {
        return  Carbon::parse($date)->format('Y-m-d H:i:s');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function asesor()
    {
        return $this->belongsTo(User::class,'asesor_id');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
