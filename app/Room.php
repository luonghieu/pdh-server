<?php

namespace App;

use App\Enums\RoomType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function unread($userId)
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at');
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

    public function getRomID($userId)
    {
        return $this->whereHas(
            'users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }
        )->where('type', '=', RoomType::DIRECT)->get()->pluck('id')->first();
    }

}
