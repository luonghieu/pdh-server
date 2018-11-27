<?php

namespace App\Notifications;

use App\Enums\DeviceType;
use App\Enums\MessageType;
use App\Enums\ProviderType;
use App\Enums\SystemMessageType;
use App\Enums\UserType;
use App\Room;
use Carbon\Carbon;
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
        return [];
    }

    public function pushData($notifiable)
    {
        $content = 'ようこそCheersへ！'
            . PHP_EOL . 'Cheersはプライベートでの飲み会や接待など様々なシーンにキャストを呼べるマッチングアプリです。'
            . PHP_EOL . PHP_EOL . 'クオリティの高いキャストと今すぐ出会えるのはCheersだけ！'
            . PHP_EOL . PHP_EOL . '呼びたいときに、呼びたい人数・場所を入力するだけ。'
            . PHP_EOL . PHP_EOL . '最短20分でキャストがゲストの元に駆けつけます♪'
            . PHP_EOL . PHP_EOL . '「キャスト一覧」からお気に入りのキャストを見つけてアピールすることも可能です！'
            . PHP_EOL . PHP_EOL . 'まずはHomeの「今すぐキャストを呼ぶ」からキャストを呼んで素敵な時間をお過ごし下さい♪'
            . PHP_EOL . PHP_EOL . 'ご不明点はお気軽にお問い合わせください。';

        $room = Room::create([
            'owner_id' => $notifiable->id
        ]);

        $room->users()->attach([1, $notifiable->id]);
        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $content,
            'system_type' => SystemMessageType::NORMAL
        ]);
        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);
        $this->limitedMessages($notifiable, $room);

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
                        'room_id' => $room->id
                    ],
                ],
                'android' => [
                    'alert' => $content,
                    'extra' => [
                        'push_id' => $pushId,
                        'send_from' => $send_from,
                        'room_id' => $room->id
                    ],
                ]
            ],
        ];
    }

    public function lineBotPushData($notifiable)
    {
        $content = 'ようこそCheersへ！'
            . PHP_EOL . 'Cheersはプライベートでの飲み会や接待など様々なシーンにキャストを呼べるマッチングアプリです。'
            . PHP_EOL . PHP_EOL . 'クオリティの高いキャストと今すぐ出会えるのはCheersだけ！'
            . PHP_EOL . PHP_EOL . '呼びたいときに、呼びたい人数・場所を入力するだけ。'
            . PHP_EOL . PHP_EOL . '最短20分でキャストがゲストの元に駆けつけます♪'
            . PHP_EOL . PHP_EOL . '「キャスト一覧」からお気に入りのキャストを見つけてアピールすることも可能です！'
            . PHP_EOL . PHP_EOL . 'まずはHomeの「今すぐキャストを呼ぶ」からキャストを呼んで素敵な時間をお過ごし下さい♪'
            . PHP_EOL . PHP_EOL . 'ご不明点はお気軽にお問い合わせください。';

        $room = Room::create([
            'owner_id' => $notifiable->id
        ]);

        $room->users()->attach([1, $notifiable->id]);
        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $content,
            'system_type' => SystemMessageType::NORMAL
        ]);
        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);
        $this->limitedMessages($notifiable, $room);

        $name = $notifiable->nickname ? $notifiable->nickname : $notifiable->name;
        $content = 'こんにちは！' . $name . 'さん🌼';
        $page = env('LINE_LIFF_REDIRECT_PAGE') . '?page=call';

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
                            'label' => '今すぐキャストを呼ぶ ',
                            'uri' => "line://app/$page"
                        ]
                    ]
                ]
            ]
        ];
    }

    private function limitedMessages($notifiable, $room)
    {
        $limitedStartTime = Carbon::parse('2018-11-22');
        $limitedEndTime = Carbon::parse('2018-11-30');
        $now = Carbon::now();
        if ($now->between($limitedStartTime, $limitedEndTime)) {
            $opContent = '【1時間無料でギャラ飲み体験🥂💓】'
                . PHP_EOL . '11月中にご利用いただいた方限定で、'
                . PHP_EOL . '30分〜1時間無料でギャラ飲みができるキャンペーン（最大11,000円OFF）を実施します✨'
                . PHP_EOL . PHP_EOL . 'ゲストからメッセージを送って、今日いけるキャストを見つける必要もありません！'
                . PHP_EOL . '「今すぐキャストを呼ぶ」から、時間/場所/人数を選ぶだけ！'
                . PHP_EOL . PHP_EOL . '※キャストは指名せずにご予約ください'
                . PHP_EOL . '※指名予約、コール内指名予約は対象外です🙇‍♀️'
                . PHP_EOL . '※対象の予約はコール予約のブロンズクラス（キャスト2名まで）のみとなります️'
                . PHP_EOL . PHP_EOL . '無料体験ができるのは11月の今だけ！️'
                . PHP_EOL . 'ぜひご利用ください🌷🌷️'
                . PHP_EOL . PHP_EOL . '詳しくは、下記の金額早見表からご確認ください♩'
                . PHP_EOL . '不明点は、メッセージ内の運営者チャットからご連絡ください。';

            $roomMessage = $room->messages()->create([
                'user_id' => 1,
                'type' => MessageType::SYSTEM,
                'message' => $opContent,
                'system_type' => SystemMessageType::NORMAL
            ]);
            $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);
        }
    }
}
