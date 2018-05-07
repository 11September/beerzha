<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'phone', 'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'role_id', 'created_at', 'updated_at', 'note',
    ];


    public function orders()
    {
        return $this->belongsTo(Order::class, 'user_id');
    }

    public function preorders()
    {
        return $this->hasMany(Preorder::class);
    }

    public function getUserToken()
    {
        return $this->token;
    }

}
