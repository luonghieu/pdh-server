<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    protected $guarded = [];

    protected $touches = ['room'];

    public function getImageAttribute($value)
    {
        if ($value) {
            return Storage::url($value);
        }
    }

    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
        }
    }

    public function unread()
    {
        return null === $this->read_at;
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipients()
    {
        return $this->belongsToMany(User::class, 'message_recipient')->withPivot('room_id')->withTimestamps();
    }
}
