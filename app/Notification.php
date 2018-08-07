<?php
namespace App;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    public function sendFrom()
    {
        return $this->belongsTo(User::class, 'send_from');
    }
}
