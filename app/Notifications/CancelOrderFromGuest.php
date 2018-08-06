<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\NotificationStyle;
use App\Enums\RoomType;
use App\Enums\SystemMessageType;
use App\Enums\UserType;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CancelOrderFromGuest extends Notification
{
    use Queueable;

    public $order;
    public $orderPoint;

    /**
     * Create a new notification instance.
     *
     * @param $order
     * @param null $orderPoint
     */
    public function __construct($order, $orderPoint = null)
    {
        $this->order = $order;
        $this->orderPoint = $orderPoint;
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
        if ($notifiable->type == UserType::GUEST) {
            $room = $notifiable->rooms()
                ->where('rooms.type', RoomType::SYSTEM)
                ->where('rooms.is_active', true)->first();
            $message = '予約のキャンセルを承りました。'
                . PHP_EOL . '--------------------------------------------------'
                . PHP_EOL . '- キャンセル内容 -'
                . PHP_EOL . '日時：' . Carbon::parse($this->order->date . ' ' . $this->order->start_time)->format('Y/m/d H:i') . '~'
                . PHP_EOL . '時間：' . $this->order->duration . '時間'
                . PHP_EOL . 'クラス：' . $this->order->castClass->name
                . PHP_EOL . '人数：' . $this->order->total_cast . '人'
                . PHP_EOL . '場所：' . $this->order->address
                . PHP_EOL . '予定合計ポイント：' . number_format($this->orderPoint) . ' Point'
                . PHP_EOL . '--------------------------------------------------'
                . PHP_EOL . PHP_EOL . 'キャンセル規定は以下の通りとなっています。'
                . PHP_EOL . '該当期間内のキャンセルについては、キャンセル料が決済されます。'
                . PHP_EOL . '当日：予約時の金額100%'
                . PHP_EOL . '1日前：予約時の金額50%'
                . PHP_EOL . '2日前〜７日前：予約時の金額30%'
                . PHP_EOL . PHP_EOL . '※キャスト都合によるキャンセルの場合、キャンセル料金はいただきません。'
                . PHP_EOL . '※ご不明点がある場合は、こちらのチャットにて、ご返信くださいませ。';

            $roomMessage = $room->messages()->create([
                'user_id' => 1,
                'type' => MessageType::SYSTEM,
                'message' => $message,
                'system_type' => SystemMessageType::NOTIFY,
            ]);

            $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);
        } else {
            $message = '予約がキャンセルされました。';
        }

        return [
            'content' => $message,
            'send_from' => UserType::ADMIN,
            'stype' => NotificationStyle::BALL
        ];
    }

    public function pushData($notifiable)
    {
        if ($notifiable->type == UserType::GUEST) {
            $content = '予約のキャンセルを承りました。'
                . PHP_EOL . '--------------------------------------------------'
                . PHP_EOL . '- キャンセル内容 -'
                . PHP_EOL . '日時：' . Carbon::parse($this->order->date . ' ' . $this->order->start_time)->format('Y/m/d H:i') . '~'
                . PHP_EOL . '時間：' . $this->order->duration . '時間'
                . PHP_EOL . 'クラス：' . $this->order->castClass->name
                . PHP_EOL . '人数：' . $this->order->total_cast . '人'
                . PHP_EOL . '場所：' . $this->order->address
                . PHP_EOL . '予定合計ポイント：' . number_format($this->orderPoint) . ' Point'
                . PHP_EOL . '--------------------------------------------------'
                . PHP_EOL . PHP_EOL . 'キャンセル規定は以下の通りとなっています。'
                . PHP_EOL . '該当期間内のキャンセルについては、キャンセル料が決済されます。'
                . PHP_EOL . '当日：予約時の金額100%'
                . PHP_EOL . '1日前：予約時の金額50%'
                . PHP_EOL . '2日前〜７日前：予約時の金額30%'
                . PHP_EOL . PHP_EOL . '※キャスト都合によるキャンセルの場合、キャンセル料金はいただきません。'
                . PHP_EOL . '※ご不明点がある場合は、こちらのチャットにて、ご返信くださいませ。';
            $pushId = 'g_9';
        } else {
            $content = '予約がキャンセルされました。';
            $pushId = 'c_9';
        }

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
                        'order_id' => $this->order->id
                    ],
                ],
            ],
        ];
    }
}
