<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'name',
        'type',
        'point',
        'time',
        'percent',
        'note',
        'is_filter_after_created_date',
        'filter_after_created_date',
        'is_filter_order_duration',
        'filter_order_duration',
    ];
    public function users()
    {
        return $this->belongsToMany('App\User', 'coupon_users', 'coupon_id','user_id')->withTimestamps();
    }

    public function orders()
    {
        return $this->belongsToMany('App\Order', 'coupon_users', 'coupon_id','order_id');
    }
}
