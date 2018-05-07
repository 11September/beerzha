<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Spectator extends Model
{
    protected $table = 'spectators';

    public static function store($method, $exception, $additional_info = null)
    {
        $spectator = new Spectator();

        $spectator->date = Carbon::now('Europe/Kiev')->toDateString();
        $spectator->time = Carbon::now('Europe/Kiev')->toTimeString();
        $spectator->method = $method;
        $spectator->line = $additional_info;
        $spectator->exception = $exception;

        $spectator->save();
    }
}


