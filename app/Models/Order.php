<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pacient_id',
        'user_id'
    ];

    public function pacient()
    {
        return $this->belongsTo(Pacient::class);
    }

    public function plannings()
    {
        return $this->hasMany(Planning::class);
    }

    public function getCreatedAtAttribute($date)
    {
        return  Carbon::parse($date)->format('Y-m-d H:i:s');
    }
}
