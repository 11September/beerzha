<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Preorder extends Model
{
    protected $table = 'preorders';

    protected $guarded = [''];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userId()
    {
        return $this->belongsTo(User::class);
    }
}
