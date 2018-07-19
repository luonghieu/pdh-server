<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'type',
        'rank',
        'cost',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
