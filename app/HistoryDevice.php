<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryDevice extends Model
{
    protected $table = "history_devices";

    public function userId()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
