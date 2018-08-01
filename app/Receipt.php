<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    public function point()
    {
        return $this->belongsTo(Point::class);
    }
}
