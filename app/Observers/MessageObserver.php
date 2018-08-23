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
        $room = $message->room;

        if (MessageType::SYSTEM == $message->type) {
            broadcast(new BroadcastMessage($message));
        }

        if (MessageType::SYSTEM != $message->type) {
            $users = $room->users->except([$message->user_id]);

            if (RoomType::DIRECT == $room->type) {
                if (!$room->checkBlocked($room->owner_id == $room->users[0]->id ? $room->users[1]->id : $room->users[0]->id)) {
                    \Notification::send($users, new MessageCreated($message));
                }
            } else {
                \Notification::send($users, new MessageCreated($message));
            }
        }

        if (RoomType::SYSTEM != $room->type || !$message->is_manual) {
            \DB::table('message_recipient')
                ->where([
                    'user_id' => $message->user_id,
                    'room_id' => $message->room_id,
                ])->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
    }
}
