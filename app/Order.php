<?php

namespace App;

use App\Enums\CastOrderType;
use App\Enums\CastOrderStatus;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function casts()
    {
        return $this->belongsToMany(Cast::class)
            ->where('cast_order.status', CastOrderStatus::ACCEPTED);
    }

    public function nominees()
    {
        return $this->belongsToMany(Cast::class)
            ->where('cast_order.type', CastOrderType::NOMINEE);
    }

    public function candidates()
    {
        return $this->belongsToMany(Cast::class)
            ->where('cast_order.type', CastOrderType::CANDIDATE);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function room()
    {
        return $this->hasOne(Room::class);
    }
}
