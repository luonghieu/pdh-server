<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function pricing()
    {
        return $this->belongsTo(Pricing::class);
    }
}
