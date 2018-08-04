<?php

namespace App;

use App\Enums\PointType;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $fillable = [
        'point',
        'balance',
        'user_id',
        'order_id',
        'is_autocharge',
        'type',
        'status',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getIsBuyAttribute()
    {
        return PointType::BUY == $this->type;
    }

    public function getIsPayAttribute()
    {
        return PointType::PAY == $this->type;
    }

    public function getIsAdjustedAttribute()
    {
        return PointType::ADJUSTED == $this->type;
    }
}
