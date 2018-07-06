<?php
namespace App;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    const SEND_FROM = [
        'user' => 1,
        'cast' => 2,
        'admin' => 3,
    ];

    public function cast()
    {
        return $this->belongsTo(Cast::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
