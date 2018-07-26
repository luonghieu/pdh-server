<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\RoomType;
use App\Enums\UserType;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;

class ApproveNominatedOrders extends Notification
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @param $order
     */
    public function __construct($order)
    {
        $this->order = $order;
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
        $startTime = Carbon::parse($this->order->date . ' ' . $this->order->start_time);
        $message = '\\\\ おめでとうございます！マッチングが確定しました♪ //'
            .'\n \n- ご予約内容 - '
            .'\n 場所：' . $this->order->address
            .'\n 合流予定時間：'. $startTime->format('H:i') .'～'
            .'\n \n ゲストの方はキャストに来て欲しい場所の詳細をお伝えください。'
            .'\n 尚、ご不明点がある場合は運営までお問い合わせください。'
            .'\n \n それでは素敵な時間をお楽しみください♪';

        return [
            'content' => $message,
            'send_from' => UserType::ADMIN,
        ];
    }

    public function pushData($notifiable)
    {
        $startTime = Carbon::parse($this->order->date . ' ' . $this->order->start_time);

        $content = '\\\\ おめでとうございます！マッチングが確定しました♪ //'
            .'\n \n- ご予約内容 - '
            .'\n 場所：' . $this->order->address
            .'\n 合流予定時間：'. $startTime->format('H:i') .'～'
            .'\n \n ゲストの方はキャストに来て欲しい場所の詳細をお伝えください。'
            .'\n 尚、ご不明点がある場合は運営までお問い合わせください。'
            .'\n \n それでは素敵な時間をお楽しみください♪';
        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;

        if ($notifiable->type == UserType::GUEST) {
            $pushId = 'g_3';
        } else {
            $pushId = 'c_3';
        }

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
