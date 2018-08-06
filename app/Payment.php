<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function point()
    {
        return $this->belongsTo(Point::class);
    }
}
