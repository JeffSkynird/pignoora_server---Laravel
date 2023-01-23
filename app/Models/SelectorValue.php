<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SelectorValue extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'value_type_id',
        'user_id'
    ];

    public function valueType()
    {
        return $this->belongsTo(ValueType::class,'value_type_id');
    }
}
