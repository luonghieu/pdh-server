<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\UserType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CreateGuest extends Notification implements ShouldQueue
{
    use Queueable;

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
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class, PushNotificationChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     */
    public function toMail($notifiable)
    {
        return;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {

        $message = 'ようこそCheersへ！'
            .'\nCheersはプライベートでの飲み会や接待など様々なシーンにキャストを呼べるマッチングアプリです。'
            .'\n \nクオリティの高いキャストと今すぐ出会えるのはCheersだけ！'
            .'\n \n呼びたいときに、呼びたい人数・場所を入力するだけ。'
            .'\n最短20分でキャストがゲストの元に駆けつけます♪'
            .'\n \n「キャスト一覧」からお気に入りのキャストを見つけてアピールすることも可能です！'
            .'\n \nまずはHomeの「今すぐキャストを呼ぶ」からキャストを呼んで素敵な時間をお過ごし下さい♪'
            .'\n \nご不明点はお気軽にお問い合わせください。';

        $room = $notifiable->rooms()->create();
        $room->users()->attach(1);

        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $message
        ]);

        $roomMessage->recipients()->attach([$notifiable->id, 1], ['room_id' => $room->id]);

        return [
            'content' => $message,
            'send_from' => UserType::ADMIN,
        ];
    }

    public function pushData($notifiable)
    {
        $content = 'ようこそCheersへ！'
            .'\nCheersはプライベートでの飲み会や接待など様々なシーンにキャストを呼べるマッチングアプリです。'
            .'\n \nクオリティの高いキャストと今すぐ出会えるのはCheersだけ！'
            .'\n \n呼びたいときに、呼びたい人数・場所を入力するだけ。'
            .'\n最短20分でキャストがゲストの元に駆けつけます♪'
            .'\n \n「キャスト一覧」からお気に入りのキャストを見つけてアピールすることも可能です！'
            .'\n \nまずはHomeの「今すぐキャストを呼ぶ」からキャストを呼んで素敵な時間をお過ごし下さい♪'
            .'\n \nご不明点はお気軽にお問い合わせください。';
        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;
        $pushId = 'g_1';

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
