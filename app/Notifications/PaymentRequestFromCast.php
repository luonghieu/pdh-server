<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\PaymentRequestStatus;
use App\Enums\ProviderType;
use App\Enums\RoomType;
use App\Enums\SystemMessageType;
use App\Enums\UserType;
use App\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PaymentRequestFromCast extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $orderPoint;

    /**
     * Create a new notification instance.
     *
     * @param $order
     * @param $orderPoint
     */
    public function __construct($order, $orderPoint)
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
        if ($notifiable->provider == ProviderType::LINE) {
            return [LineBotNotificationChannel::class];
        } else {
            return [PushNotificationChannel::class];
        }
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
        $guestNickname = $this->order->user->nickname ? $this->order->user->nickname . '様' : 'お客様';
        $requestedStatuses = [
            PaymentRequestStatus::OPEN,
            PaymentRequestStatus::REQUESTED,
            PaymentRequestStatus::UPDATED,
        ];
        $totalPoint = Order::find($this->order->id)->paymentRequests()->whereIn('status', $requestedStatuses)->sum('total_point');
        $content = 'Cheersをご利用いただきありがとうございました♪'
        . PHP_EOL . $orderStartDate->format('Y/m/d H:i') . '~' . 'の合計ポイントは' .
            number_format($totalPoint) . 'Pointです。'
            . PHP_EOL . '合計ポイントの詳細はコチラから確認することができます。'
            . PHP_EOL . '※詳細に誤りがある場合は、24時間以内に「決済ポイントの修正依頼をする」を押してください。運営から確認のご連絡を差し上げます。'
            . PHP_EOL . PHP_EOL . 'ご不明点がございましたらいつでもお問い合わせください。'
            . PHP_EOL . PHP_EOL . $guestNickname . 'のまたのご利用をお待ちしております♪';

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
                        'room_id' => $room->id
                    ],
                ],
                'android' => [
                    'alert' => $content,
                    'extra' => [
                        'push_id' => $pushId,
                        'send_from' => $send_from,
                        'order_id' => $this->order->id,
                        'room_id' => $room->id
                    ],
                ]
            ],
        ];
    }

    public function lineBotPushData($notifiable)
    {
        $orderStartDate = Carbon::parse($this->order->date . ' ' . $this->order->start_time);
        $guestNickname = $this->order->user->nickname ? $this->order->user->nickname . '様' : 'お客様';
        $requestedStatuses = [
            PaymentRequestStatus::OPEN,
            PaymentRequestStatus::REQUESTED,
            PaymentRequestStatus::UPDATED,
        ];
        $totalPoint = Order::find($this->order->id)->paymentRequests()->whereIn('status', $requestedStatuses)->sum('total_point');
        $content = 'Cheersをご利用いただきありがとうございました♪'
            . PHP_EOL . $orderStartDate->format('Y/m/d H:i') . '~' . 'の合計ポイントは' .
            number_format($totalPoint) . 'Pointです。'
            . PHP_EOL . '合計ポイントの詳細はコチラから確認することができます。'
            . PHP_EOL . '※詳細に誤りがある場合は、24時間以内に「決済ポイントの修正依頼をする」を押してください。運営から確認のご連絡を差し上げます。'
            . PHP_EOL . PHP_EOL . 'ご不明点がございましたらいつでもお問い合わせください。'
            . PHP_EOL . PHP_EOL . $guestNickname . 'のまたのご利用をお待ちしております♪';

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

        $content = 'Cheersをご利用いただきありがとうございました♫'
            . PHP_EOL . '「評価・決済する」をタップして、本日の飲み会の評価と決済をお願いします。'
            . PHP_EOL . 'またのご利用をお待ちしております😁💫';

        $page = env('LINE_LIFF_REDIRECT_PAGE') . '?page=evaluation&order_id=' . $this->order->id;

        return [
            [
                'type' => 'template',
                'altText' => $content,
                'text' => $content,
                'template' => [
                    'type' => 'buttons',
                    'text' => $content,
                    'actions' => [
                        [
                            'type' => 'uri',
                            'label' => '評価・決済する ',
                            'uri' => "line://app/$page"
                        ]
                    ]
                ]
            ]
        ];
    }
}