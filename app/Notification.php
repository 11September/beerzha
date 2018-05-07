<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Notification extends Model
{
    protected $table = "notifications";

    protected $fillable = [];

    protected $dates = ['created_at', 'date_start', 'date_end'];

    public function scopePublished($query)
    {
        return $query->where('status', '=', 'Published');
    }

    public function scopePending($query)
    {
        return $query->where('status', '=', 'Pending');
    }

    public function scopeClose($query)
    {
        return $query->where('status', '=', 'Closed');
    }
}
