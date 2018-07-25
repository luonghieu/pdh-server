<?php

Broadcast::channel('room.{room}', function ($user, App\Room $room) {
    return $room->users->contains($user->id);
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return $user->id == $userId;
});
