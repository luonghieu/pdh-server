<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $casts = [
        'cast_ids' => 'array',
    ];

    protected $fillable = [
        'prefecture_id',
        'comment',
        'date',
        'start_time_from',
        'start_time_to',
        'duration',
        'cast_ids',
        'total_cast',
        'temp_point',
        'class_id',
        'status',
    ];

    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
