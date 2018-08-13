<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\RoomType;
use App\Enums\SystemMessageType;
use App\Enums\UserType;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PaymentRequestFromCast extends Notification implements ShouldQueue
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
        return [PushNotificationChannel::class];
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
        return [
            //
        ];
    }

    public function pushData($notifiable)
    {
        $orderStartDate = Carbon::parse($this->order->date . ' ' . $this->order->start_time);
        $orderEndDate = Carbon::parse($this->order->actual_ended_at);
        $guestNickname = $this->order->user->nickname ? $this->order->user->nickname . '様' : 'お客様';

        $content = 'Cheersをご利用いただきありがとうございました♪'
        . PHP_EOL . $orderStartDate->format('Y/m/d H:i') . '~' . $orderEndDate->format('H:i') . 'の合計ポイントは' .
        $this->order->total_point . 'Pointです。'
            . PHP_EOL . '合計ポイントの詳細はコチラから確認することができます。'
            . PHP_EOL . '※詳細に誤りがある場合は、24時間以内に「決済ポイントの修正依頼をする」を押してください。運営から確認のご連絡を差し上げます。'
            . PHP_EOL . PHP_EOL . 'ご不明点がございましたらいつでもお問い合わせください。'
            . PHP_EOL . PHP_EOL . $guestNickname . 'またのご利用をお待ちしております♪';

        $room = $notifiable->rooms()
            ->where('rooms.type', RoomType::SYSTEM)
            ->where('rooms.is_active', true)->first();
        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $content,
            'system_type' => SystemMessageType::NORMAL,
            'order_id' => $this->order->id,
        ]);
        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);

        $pushId = 'g_15';
        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;

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
                        'order_id' => $this->order->id,
                    ],
                ],
            ],
        ];
    }
}
