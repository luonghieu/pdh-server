<?php

namespace App;

use App\Enums\OrderStatus;
use App\Enums\RoomType;
use App\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Room extends Model
{
    protected $guarded = [];

    public function getUnreadCountAttribute()
    {
        if (Auth::check()) {
            return $this->unread(Auth::user()->id)->count();
        }

        return 0;
    }

    public function getIsActiveAttribute($value)
    {
        return $value ? 1 : 0;
    }

    public function getIsSystemAttribute()
    {
        return RoomType::SYSTEM == $this->type;
    }

    public function getIsDirectAttribute()
    {
        return RoomType::DIRECT == $this->type;
    }

    public function getIsGroupAttribute()
    {
        return RoomType::GROUP == $this->type;
    }

    public function checkBlocked($id)
    {
        return $this->owner->blockers->contains($id) || $this->owner->blocks->contains($id) ? 1 : 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDirect($query)
    {
        return $query->where('type', RoomType::DIRECT);
    }

    public function unread($userId)
    {
        return $this->messages()
            ->whereHas('recipients', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->whereNull('read_at');
            });
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function getRoomOrderAttribute()
    {
        $order = null;

        switch ($this->type) {
            case RoomType::GROUP:
                $order = $this->order;
                break;
            case RoomType::DIRECT:
                $data = [
                    OrderStatus::PROCESSING,
                    OrderStatus::ACTIVE,
                    OrderStatus::DONE,
                ];

                $userIds = $this->users->pluck('id');

                $order = Order::where(function ($q) use ($userIds) {
                    $q->whereHas('nominees', function ($q) use ($userIds) {
                        $q->whereIn('user_id', $userIds);
                    })->orWhereHas('candidates', function ($q) use ($userIds) {
                        $q->whereIn('user_id', $userIds);
                    });
                })
                ->whereIn('status', $data)
                ->orderByRaw('FIELD(status, ' . implode(',', $data) . ' )')
                ->first();
                break;
            default:
                break;
        }

        return $order;
    }
}
