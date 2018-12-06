<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OfferMessageNotifyToLine extends Notification implements ShouldQueue
{
    use Queueable;

    public $offerId;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($offerId)
    {
        $this->offerId = $offerId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [LineBotNotificationChannel::class];
    }

    public function lineBotPushData($notifiable)
    {
        $content = $notifiable->nickname . 'さんにキャストから' . PHP_EOL . 'オファーが届きました！';
        $page = env('LINE_LIFF_REDIRECT_PAGE') . '?page=offers&offer_id=' . $this->offerId;

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
                            'label' => 'オファー内容をみてみる',
                            'uri' => "line://app/$page",
                        ],
                    ],
                ],
            ],
        ];
    }
}
