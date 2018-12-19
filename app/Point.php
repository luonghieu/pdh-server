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
        'is_transfered',
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
        return $this->belongsTo(Order::class)->withTrashed();
    }

    public function getIsBuyAttribute()
    {
        return PointType::BUY == $this->type;
    }

    public function getIsPayAttribute()
    {
        return PointType::PAY == $this->type;
    }

    public function getIsAutoChargeAttribute()
    {
        return PointType::AUTO_CHARGE == $this->type;
    }

    public function getIsAdjustedAttribute()
    {
        return PointType::ADJUSTED == $this->type;
    }

    public function getIsReceiveAttribute()
    {
        return PointType::RECEIVE == $this->type;
    }

    public function getIsTransferAttribute()
    {
        return PointType::TRANSFER == $this->type;
    }

    public function createPoint($data = [], $status = false)
    {
        $this->point = $data['point'];
        $this->balance = $data['balance'];
        $this->user_id = $data['user_id'];
        $this->type = $data['type'];
        $this->status = $status;

        if (isset($data['order_id'])) {
            $this->order_id = $data['order_id'];
        }
        $this->save();
    }
}
