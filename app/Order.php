<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    public function orderClass()
    {
        return $this->belongsTo(CastClass::class, 'class_id', 'id');
    }
}
