<?php

namespace App\Observers;

use App\Enums\MessageType;
use App\Message;
use App\Notifications\MessageCreated;

class MessageObserver
{
    public function created(Message $message)
    {
        if (MessageType::SYSTEM != $message->type) {
            $users = ($message->room->users->except([$message->user_id]));
            \Notification::send($users, new MessageCreated($message));
        }

        $messages = $message->room->unread($message->user_id)->get();

        foreach ($messages as $mess) {
            $mess->recipients()->updateExistingPivot($message->user_id, ['read_at' => now()],
                false
            );
        }
    }
}
