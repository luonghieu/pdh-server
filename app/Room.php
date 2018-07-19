<?php

namespace App;

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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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

    public function recipient()
    {
        return $this->belongsToMany(User::class, 'message_recipient', 'room_id', 'user_id')->withPivot('room_id', 'read_at')->withTimestamps();
    }
}
