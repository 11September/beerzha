<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beerga extends Model
{
    protected $table = 'beerga';


    public static function getDecreasePriceStep()
    {
        $PriceStep = Beerga::where('key', 'getDecreasePriceStep')->first();

        return $PriceStep->value;
    }

    public static function getIncreasePriceStep()
    {
        $PriceStep = Beerga::where('key', 'getIncreasePriceStep')->first();

        return $PriceStep->value;
    }

    public static function getKeyLength()
    {
        $key_length = Beerga::where('key', 'key_length')->first();

        return $key_length->value;
    }


    public static function getCode()
    {
        $code = Beerga::where('key', "code")->first();

        return $code->value;
    }
}
