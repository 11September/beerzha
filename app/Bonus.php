<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Bonus extends Model
{
    public function userId()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function frameId()
    {
        return $this->belongsTo(Frame::class, 'frame_id', 'id');
    }
}
