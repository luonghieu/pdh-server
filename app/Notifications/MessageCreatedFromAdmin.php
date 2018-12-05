<?php

namespace App\Notifications;

use App\Enums\DeviceType;
use App\Enums\UserType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class MessageCreatedFromAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    public $room_id;

    /**
     * Create a new notification instance.
     * @param $roomId
     */
    public function __construct($roomId)
    {
        $this->room_id = $roomId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($notifiable->device_type == DeviceType::WEB && $notifiable->type == UserType::GUEST) {
            if ($notifiable->is_verified) {
                return [LineBotNotificationChannel::class, TwilioChannel::class];
            }

            return [LineBotNotificationChannel::class];
        }

        return [];
    }

    public function lineBotPushData($notifiable)
    {

        $content = 'Cheers 運営局から新着メッセージが届きました。';
        $page = env('LINE_LIFF_REDIRECT_PAGE') . '?page=room&room_id=' . $this->room_id;

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
                            'label' => 'メッセージを確認する',
                            'uri' => "line://app/$page"
                        ]
                    ]
                ]
            ]
        ];
    }

    public function toTwilio($notifiable)
    {
        $content = '[Cheers]運営局から新着メッセージが届きました。'
            . PHP_EOL . 'CheersのLINE内にあるメニューから、メッセージをご確認ください。';

        return (new TwilioSmsMessage())
            ->content($content);
    }
}
