<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Storage;

class MarketingOperation extends Notification implements ShouldQueue
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
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $now = Carbon::now();
        $createdAt = Carbon::parse($notifiable->created_at);
        $dayFive = $createdAt->copy()->addDays(5)->startOfDay();
        $daySix = $createdAt->copy()->addDays(6)->endOfDay();
        if ($now->between($dayFive, $daySix)) {
            return [LineBotNotificationChannel::class];
        }

        return [];
    }

    public function lineBotPushData($notifiable)
    {
        $pricesSrc = Storage::url('add_friend_price_063112.png');
        $bannerSrc = Storage::url('add_friend_banner_063112.jpg');
        if (!@getimagesize($pricesSrc)) {
            $fileContents = Storage::disk('local')->get("system_images/add_friend_price_063112.png");
            $fileName = 'add_friend_price_063112.png';
            Storage::put($fileName, $fileContents, 'public');
        }
        if (!@getimagesize($bannerSrc)) {
            $fileContents = Storage::disk('local')->get("system_images/add_friend_banner_063112.jpg");
            $fileName = 'add_friend_banner_063112.jpg';
            Storage::put($fileName, $fileContents, 'public');
        }

        $message = '【新規ユーザー様限定！ギャラ飲み1時間無料🥂💕】'
            . PHP_EOL . PHP_EOL . 'Cheersにご登録いただいてから1週間以内のゲスト様限定で、1時間無料キャンペーンを実施中！✨'
            . PHP_EOL . PHP_EOL . '※予約方法は、コール予約、指名予約問いません。'
            . PHP_EOL . '2時間以上のご予約で1時間無料となります（最大11,000円OFF）'
            . PHP_EOL . PHP_EOL . 'ギャラ飲み初めての方も安心！'
            . PHP_EOL . 'Cheersのキャストが盛り上げます🙋‍♀️❤️'
            . PHP_EOL . '忘年会の季節に、キャストを呼んで飲み会や接待を盛り上げませんか？'
            . PHP_EOL . PHP_EOL . 'ご登録から1週間を超えてしまうとキャンペーン対象外となりますのでお早めにご予約ください。'
            . PHP_EOL . PHP_EOL . 'ご不明点はメッセージ内の運営者チャットからご連絡ください！';

        return  [
            [
                'type' => 'text',
                'text' => $message
            ],
            [
                'type' => 'image',
                'originalContentUrl' => $pricesSrc,
                'previewImageUrl' => $pricesSrc

            ],
            [
                'type' => 'image',
                'originalContentUrl' => $bannerSrc,
                'previewImageUrl' => $bannerSrc
            ]
        ];
    }
}
