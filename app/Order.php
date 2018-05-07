<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "orders";

    protected $fillable = ['id','user_id', 'dish_id', 'amount', 'payment', 'price', 'seat', 'status'];

    protected $dates = ['created_at', 'time'];

    protected $visible = ['id', 'dish', 'amount'];

    public function getCreatedAtAttribute($date)
    {
        return $this->attributes['created_at'] = Carbon::parse($date)->toDateString();
    }

    public function getTimeAttribute($date)
    {
        return $this->attributes['time'] = Carbon::parse($date)->toDateString();
    }

    public function dishId()
    {
        return $this->hasOne(Dish::class, 'id', 'dish_id');
    }

    public function userId()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function dish()
    {
        return $this->hasOne(Dish::class, 'id', 'dish_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
