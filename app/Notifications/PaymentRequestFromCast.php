<?php

namespace App\Notifications;

use App\Enums\DeviceType;
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
    public $paymentRequests;
    public $totalPoint;
    public $extraPoint;
    public $hasExtraTime;

    /**
     * Create a new notification instance.
     *
     * @param $order
     * @param $orderPoint
     * @param null $paymentRequest
     */
    public function __construct($order, $orderPoint)
    {
        $this->order = $order;
        $this->orderPoint = $orderPoint;

        $requestedStatuses = [
            PaymentRequestStatus::OPEN,
            PaymentRequestStatus::REQUESTED,
            PaymentRequestStatus::UPDATED,
            PaymentRequestStatus::CLOSED,
            PaymentRequestStatus::CONFIRM
        ];
        $this->totalPoint = 0;
        $this->extraPoint = 0;
        $this->hasExtraTime = false;
        $paymentRequests =  Order::find($this->order->id)->paymentRequests()->whereIn('status', $requestedStatuses)->get();
        foreach ($paymentRequests as $payment) {
            if ($payment->extra_time) {
                $this->hasExtraTime = true;
            }
            $this->totalPoint += $payment->total_point;
            $this->extraPoint += $payment->extra_point;
        }

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($this->hasExtraTime) {
            if ($notifiable->provider == ProviderType::LINE) {
                if ($notifiable->type == UserType::GUEST && $notifiable->device_type == null) {
                    return [LineBotNotificationChannel::class];
                }

                if ($notifiable->type == UserType::CAST && $notifiable->device_type == null) {
                    return [PushNotificationChannel::class];
                }

                if ($notifiable->device_type == DeviceType::WEB) {
                    return [LineBotNotificationChannel::class];
                } else {
                    return [PushNotificationChannel::class];
                }
            } else {
                return [PushNotificationChannel::class];
            }
        } else {
            return [];
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
//        $content = 'Cheersをご利用いただきありがとうございました♪'
//        . PHP_EOL . $orderStartDate->format('Y/m/d H:i') . '~' . 'の合計ポイントは' . number_format($totalPoint) . 'Pointです。'
//            . PHP_EOL . 'お手数ですがコチラから、本日の飲み会の評価と決済を行ってください。'
//            . PHP_EOL . '※詳細に誤りがある場合は、3時間以内に「決済ポイントの修正依頼をする」を押してください。運営から確認のご連絡を差し上げます。'
//            . PHP_EOL . '※3時間以内に決済が行われなかった場合は、不足分のポイントを自動で決済させていただきますので、ご了承ください。'
//            . PHP_EOL . PHP_EOL . 'ご不明点がございましたらいつでもお問い合わせください。'
//            . PHP_EOL . PHP_EOL . $guestNickname . 'のまたのご利用をお待ちしております♪';
        $content = 'Cheersをご利用いただきありがとうございました♪'
            . PHP_EOL . $orderStartDate->format('Y/m/d H:i') . '~' . 'の合計ポイントは' . number_format($this->totalPoint) . 'Pointです。'
            . PHP_EOL . '延長料金が' . $this->extraPoint . 'Point発生しておりますので、別途運営から決済画面をお送りいたします。';

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
//        $content = 'Cheersをご利用いただきありがとうございました♪'
//            . PHP_EOL . $orderStartDate->format('Y/m/d H:i') . '~' . 'の合計ポイントは' . number_format($totalPoint) . 'Pointです。'
//            . PHP_EOL . 'お手数ですがコチラから、本日の飲み会の評価と決済を行ってください。'
//            . PHP_EOL . '※詳細に誤りがある場合は、3時間以内に「決済ポイントの修正依頼をする」を押してください。運営から確認のご連絡を差し上げます。'
//            . PHP_EOL . '※3時間以内に決済が行われなかった場合は、不足分のポイントを自動で決済させていただきますので、ご了承ください。'
//            . PHP_EOL . PHP_EOL . 'ご不明点がございましたらいつでもお問い合わせください。'
//            . PHP_EOL . PHP_EOL . $guestNickname . 'のまたのご利用をお待ちしております♪';

        $roomMessageContent = 'Cheersをご利用いただきありがとうございました♪'
            . PHP_EOL . $orderStartDate->format('Y/m/d H:i') . '~' . 'の合計ポイントは' . number_format($this->totalPoint) . 'Point です。'
            . PHP_EOL . '延長料金が' . $this->extraPoint . 'Point発生しておりますので、別途運営から決済画面をお送りいたします。';

        $lineMessageContent = 'Cheersをご利用いただきありがとうございました♪'
            . PHP_EOL . '延長料金が' . number_format($this->extraPoint) . 'Point 発生しておりますので、別途運営から決済画面をお送りいたします。';


        $room = $notifiable->rooms()
            ->where('rooms.type', RoomType::SYSTEM)
            ->where('rooms.is_active', true)->first();
        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $roomMessageContent,
            'system_type' => SystemMessageType::NORMAL,
            'order_id' => $this->order->id,
        ]);
        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);

//        $content = 'Cheersをご利用いただきありがとうございました♫'
//            . PHP_EOL . '「評価・決済する」をタップして、本日の飲み会の評価と決済をお願いします。'
//            . PHP_EOL . 'またのご利用をお待ちしております😁💫';
        $page = env('LINE_LIFF_REDIRECT_PAGE') . '?page=evaluation&order_id=' . $this->order->id;

        return [
            [
                'type' => 'text',
                'text' => $lineMessageContent
            ]
        ];
    }
}
