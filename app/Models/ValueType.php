<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ValueType extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'is_multiple',
        'user_id'
    ];
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function selectorValues()
    {
        return $this->hasMany(SelectorValue::class);
    }
}
