<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    public function room()
    {
        return $this->hasOne(Room::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function casts()
    {
        return $this->belongsToMany(Cast::class);
    }
}
