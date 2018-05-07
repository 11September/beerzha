<?php

namespace App;

use App\Dish;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $table = 'types';

    protected $fillable = [];
    protected $hidden = ['created_at', 'updated_at'];


    public function dishes()
    {
        return $this->hasMany(Dish::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', '=', 'PUBLISHED');
    }

    public function scopeBonuses($query)
    {
        return $query->where('bonuses', 'Yes');
    }

    public function scopeFilter($query, $params)
    {
        if ( $bonuses = array_get($params, 'bonuses') )
        {
            $query = $query->where('bonuses', '=' , $bonuses);
        }

        if ( $type = array_get($params, 'type') )
        {
            $query = $query->where('type', '=' , $type);
        }



        return $query;
    }

}
