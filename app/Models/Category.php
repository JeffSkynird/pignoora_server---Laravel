<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'image',
        'description'
    ];
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
    public function getCreatedAtAttribute($date)
    {
        return  Carbon::parse($date)->format('Y-m-d H:i:s');
    }
    
}
