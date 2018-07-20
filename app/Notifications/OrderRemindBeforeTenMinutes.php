<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\RoomType;
use App\Enums\UserType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderRemindBeforeTenMinutes extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class, PushNotificationChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable)
    {
        return;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $message = '合流予定時刻の10分前になりました！'
            . '\n ゲストとのチャット画面にスタートボタンが出現します。'
            . '\n 合流後、ゲストに確認してからスタートボタンを押してください！'
            . '\n \n それでは素敵な時間を楽しんで来てくださいね♪'
            . '\n また予定時刻に遅れそうな場合は、チャットルームで予め遅れる旨を伝えましょう！';

        $room = $notifiable->rooms()
            ->where('rooms.type', RoomType::SYSTEM)
            ->where('rooms.is_active', true)->first();

        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $message
        ]);

        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);

        return [
            'content' => $message,
            'send_from' => UserType::ADMIN,
        ];
    }

    public function pushData($notifiable)
    {
        $content = '合流予定時刻の10分前になりました！'
            . '\n ゲストとのチャット画面にスタートボタンが出現します。'
            . '\n 合流後、ゲストに確認してからスタートボタンを押してください！'
            . '\n \n それでは素敵な時間を楽しんで来てくださいね♪'
            . '\n また予定時刻に遅れそうな場合は、チャットルームで予め遅れる旨を伝えましょう！';
        $namedUser = 'mikke_dev';
        $send_from = UserType::ADMIN;
        $pushId = 'c_1';

        return [
            'audienceOptions' => ['named_user' => $namedUser],
            'notificationOptions' => [
                'alert' => $content,
                'ios' => [
                    'alert' => $content,
                    'sound' => 'cat.caf',
                    'badge' => '+1',
                    'content-available' => true,
                    'extra' => [
                        'push_id' => $pushId,
                        'send_from' => $send_from,
                    ],
                ],
            ],
        ];
    }
}
