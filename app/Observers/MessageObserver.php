<?php

namespace App\Observers;

use App\Enums\MessageType;
use App\Message;
use App\Notifications\MessageCreated;

class MessageObserver
{
    public function created(Message $message)
    {
        $users = ($message->room->users->except([$message->user_id]));
        \Notification::send($users, new MessageCreated($message));
    }
}
