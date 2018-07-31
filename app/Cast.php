<?php

namespace App;

use App\Enums\UserType;
use App\Traits\HasParentModel;

class Cast extends User
{
    use HasParentModel;

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            $query->where('users.type', UserType::CAST);
        });
    }

    public function castClass()
    {
        return $this->belongsTo(CastClass::class, 'class_id', 'id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'cast_order', 'user_id', 'order_id');
    }

    public function getLatestOrderAttribute()
    {
        return $this
            ->orders()
            ->whereNotNull('cast_order.accepted_at')
            ->whereNull('cast_order.canceled_at')
            ->latest()
            ->first();
    }
}
