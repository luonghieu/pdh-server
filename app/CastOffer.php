<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CastOffer extends Model
{
    protected $fillable = [
        'date',
        'start_time',
        'guest_id',
        'duration',
        'address',
        'cast_class_id',
        'cost',
        'temp_point',
        'prefecture_id',
    ];

    public function cast()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function guest()
    {
        return $this->hasOne(User::class, 'id', 'guest_id');
    }

    public function castClass()
    {
        return $this->hasOne(CastClass::class, 'id', 'cast_class_id');
    }
}
