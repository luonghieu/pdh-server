<?php

namespace App;

use App\Enums\OrderStatus;
use App\Enums\UserType;
use App\Traits\HasParentModel;
use Illuminate\Support\Facades\Auth;

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
        return $this->belongsToMany(Order::class, 'cast_order', 'user_id', 'order_id')
            ->withPivot('created_at', 'updated_at');
    }

    public function getLatestOrderAttribute()
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();

        return $this
            ->orders()
            ->where([
                ['orders.user_id', '=', $user->id],
                ['orders.status', '=', OrderStatus::DONE],
            ])
            ->whereNotNull('cast_order.accepted_at')
            ->whereNull('cast_order.canceled_at')
            ->latest()
            ->first();
    }

    public function castRanking()
    {
        return $this->hasOne(CastRanking::class);
    }
}