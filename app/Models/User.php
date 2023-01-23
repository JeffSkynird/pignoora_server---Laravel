<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
  
    protected $guard_name ='api';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'address',
        'phone',
        'names',
        'last_names',
        'email',
        'password',
        'is_admin',
        'tipo_user'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
    public function setNamesAttribute($value)
    {
        $this->attributes['names'] = ucwords(strtolower($value));
    }
    public function setLastNamesAttribute($value)
    {
        $this->attributes['last_names'] = ucwords(strtolower($value));
    }
    public function getCreatedAtAttribute($date)
    {
        return  Carbon::parse($date)->format('Y-m-d H:i:s');
    }
    public function pawn()
    {
        return $this->hasMany(Pawn::class);
    }
}
