<?php

namespace App\Observers;

use App\Enums\MessageType;
use App\Enums\RoomType;
use App\Events\MessageCreated as BroadcastMessage;
use App\Message;
use App\Notifications\MessageCreated;

class MessageObserver
{
    public function created(Message $message)
    {
        if (MessageType::SYSTEM == $message->type) {
            broadcast(new BroadcastMessage($message));
        }

        if (MessageType::SYSTEM != $message->type) {
            $users = $message->room->users->except([$message->user_id]);

            if (RoomType::DIRECT == $message->room->type) {
                if (!$message->room->checkBlocked($message->room->owner_id == $message->room->users[0]->id ? $message->room->users[1]->id : $message->room->users[0]->id)) {
                    \Notification::send($users, new MessageCreated($message));
                }
            } else {
                \Notification::send($users, new MessageCreated($message));
            }
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
