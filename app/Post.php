<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = "posts";

    protected $fillable = [];

    protected $dates = ['created_at'];

    public function getCreatedAtAttribute($date)
    {
        return $this->attributes['created_at'] = Carbon::parse($date)->toDateString();
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', '=', 'PUBLISHED');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', '=', 'DRAFT');
    }

    public function scopePending($query)
    {
        return $query->where('status', '=', 'PENDING');
    }

    public function scopeFilter($query, $params)
    {
        if ($id = array_get($params, 'id')) {
            $query = $query->where('id', '=', $id);
        }

        if ($title = array_get($params, 'title')) {
            $query = $query->where('title', '=', $title);
        }

        if ($sort_title_asc = array_has($params, 'sort_title_asc')) {
            $query = $query->orderBy('title', 'asc');
        }

        if ($sort_title_desc = array_has($params, 'sort_title_desc')) {
            $query = $query->orderBy('title', 'desc');
        }

        if ($title_like = array_get($params, 'title_like')) {
            $query = $query->where('title', 'like', ('%' . $title_like . '%'));
        }

        if ($type = array_get($params, 'type')) {
            $query = $query->where('type_id', '=', $type);
        }

        if ($price = array_get($params, 'price')) {
            $query = $query->where('price', '=', $price);
        }

        if ($price_from = array_get($params, 'price_from') && !$price_from = array_get($params, 'price_to')) {
            $query = $query->where('price', '>=', $price_from);
        }

        if ($price_to = array_get($params, 'price_to') && !$price_to = array_get($params, 'price_from')) {
            $query = $query->where('price', '<=', trim($params['price_to']));
        }

        if ($price_from = array_get($params, 'price_from') && $price_to = array_get($params, 'price_to')) {
            $query = $query->whereBetween('price', [$price_from, $price_to]);
        }

        if ($sort_price_asc = array_get($params, 'sort_price_asc')) {
            $query = $query->orderBy('name', 'asc');
        }

        if ($sort_price_desc = array_get($params, 'sort_price_desc')) {
            $query = $query->orderBy('name', 'desc');
        }

        if ($sort_id_asc = array_has($params, 'sort_id_asc')) {
            $query = $query->orderBy('id', 'asc');
        }

        if ($sort_id_desc = array_has($params, 'sort_id_desc')) {
            $query = $query->orderBy('id', 'desc');
        }

        if ($status = array_get($params, 'status')) {
            $query = $query->where('status', '=', $status);
        }

        return $query;
    }
}
