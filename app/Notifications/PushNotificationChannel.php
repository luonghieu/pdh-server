<?php

namespace App\Notifications;

use App\Services\UrbanAirship;
use App\User;
use Illuminate\Notifications\Notification;

class PushNotificationChannel
{

    public function send($notifiable, Notification $notification)
    {
        $appKey = config('urbanairship.app_key');
        $masterSecret = config('urbanairship.master_secret');

        $urbanAirship = new UrbanAirship($appKey, $masterSecret);

        $data = $notification->pushData($notifiable);

        return $urbanAirship->push($data['audienceOptions'], $data['notificationOptions']);
    }
}