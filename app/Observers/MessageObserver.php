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

        \DB::table('message_recipient')
            ->where([
                'user_id' => $message->user_id,
                'room_id' => $message->room_id,
            ])
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
