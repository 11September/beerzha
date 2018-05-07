<?php

namespace App;

use App\Beerga;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Beer extends Model
{
    protected $table = "beers";

    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_order',
    ];

    public function getCreatedAtAttribute($date)
    {
        return $this->attributes['created_at'] = Carbon::parse($date)->toDateString();
    }

    public function scopePublished($query)
    {
        $query->where('status', '=', 'PUBLISHED');
    }

    public function scopeUnpublished($query)
    {
        $query->where('status', '=', 'PUBLISHED');
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

        if ($price = array_get($params, 'price')) {
            $query = $query->where('price_stable', '=', $price);
        }

        if ($price_from = array_get($params, 'price_stable_from') && !$price_from = array_get($params, 'price_stable_to')) {
            $query = $query->where('price_stable', '>=', $price_from);
        }

        if ($price_to = array_get($params, 'price_stable_to') && !$price_to = array_get($params, 'price_stable_from')) {
            $query = $query->where('price_stable', '<=', trim($params['price_to']));
        }

        if ($price_from = array_get($params, 'price_stable_from') && $price_to = array_get($params, 'price_stable_to')) {
            $query = $query->whereBetween('price_stable', [$price_from, $price_to]);
        }

        if ($sort_price_asc = array_get($params, 'sort_price_asc')) {
            $query = $query->orderBy('price_stable', 'asc');
        }

        if ($sort_price_desc = array_get($params, 'sort_price_desc')) {
            $query = $query->orderBy('price_stable', 'desc');
        }

        if ($price = array_get($params, 'price_quotations')) {
            $query = $query->where('price_quotations', '=', $price);
        }

        if ($price_from = array_get($params, 'price_quotations_from') && !$price_from = array_get($params, 'price_quotations_to')) {
            $query = $query->where('price_quotations', '>=', $price_from);
        }

        if ($price_to = array_get($params, 'price_quotations_to') && !$price_to = array_get($params, 'price_quotations_from')) {
            $query = $query->where('price_quotations', '<=', trim($params['price_to']));
        }

        if ($price_from = array_get($params, 'price_quotations_from') && $price_to = array_get($params, 'price_quotations_to')) {
            $query = $query->whereBetween('price_quotations', [$price_from, $price_to]);
        }

        if ($sort_price_asc = array_get($params, 'price_quotations_asc')) {
            $query = $query->orderBy('price_quotations', 'asc');
        }

        if ($sort_price_desc = array_get($params, 'price_quotations_desc')) {
            $query = $query->orderBy('price_quotations', 'desc');
        }

        if ($sort_id_asc = array_has($params, 'sort_id_asc')) {
            $query = $query->orderBy('id', 'asc');
        }

        if ($sort_id_desc = array_has($params, 'sort_id_desc')) {
            $query = $query->orderBy('id', 'desc');
        }

        return $query;
    }

    public static function DecreasePercent($min, $price, $price_quotations = null)
    {
        $result_array = [];
        $step = floatval(Beerga::getDecreasePriceStep());

        if (!$step) {
            $step = 1;
        }

        if ($price_quotations == null || $price_quotations == 0) {
            $price_quotations = $price;
        }

        if (($price_quotations - $step) <= $min) {
            $price_quotations = $min;
            $percent = ($price_quotations - $price) / $price * 100;
        } else {
            $price_quotations = $price_quotations - $step;
            $percent = ($price_quotations - $price) / $price * 100;
        }

        $result_array['price_quotations'] = $price_quotations;
        $result_array['percent'] = $percent;

        return $result_array;
    }

    public static function IncreasePercent($min, $max, $price, $price_quotations = null)
    {
        $result_array = [];
        $step = floatval(Beerga::getIncreasePriceStep());

        if (!$step) {
            $step = 1;
        }

        if ($price_quotations == null || $price_quotations == 0 || !$price_quotations || $price_quotations < $min) {
            $price_quotations = $price;
        }

        if (($price_quotations + $step) >= $max) {
            $price_quotations = $max;
            $percent = ($price_quotations - $price) / $price * 100;
        } else {
            $price_quotations = $price_quotations + $step;
            $percent = ($price_quotations - $price) / $price * 100;
        }

        $result_array['price_quotations'] = $price_quotations;
        $result_array['percent'] = $percent;

        return $result_array;
    }
}
