<?php

namespace App\Console\Commands;

use App\Enums\MessageType;
use App\Enums\OrderStatus;
use App\Enums\SystemMessageType;
use App\Notifications\RenewalReminderTenMinute;
use App\Notifications\TenMinBeforeOrderEnded;
use App\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendRemindBeforeEnDingTimeTenMins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheers:send_remind_before_ending_time_ten_mins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = ' Send remind for cast and guest before order ending time 10 mins';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now()->second(0);
        $currentDate = Carbon::now()->format('Y-m-d');
        $orders = Order::whereDate('date', $currentDate)->whereIn('status', [OrderStatus::PROCESSING])->with('casts')->get();

        foreach ($orders as $order) {
            $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $order->date . ' ' . $order->end_time)->second(0);

            $time = $endTime->copy()->subMinute(10);

            if ($time == $now) {
                $room = $order->room;
                $messageForCast = '解散予定時刻まで残り10分です！'
                    . PHP_EOL . '解散予定時刻後は自動で延長されます。';

                $roomMessage = $room->messages()->create([
                    'user_id' => 1,
                    'type' => MessageType::SYSTEM,
                    'message' => $messageForCast,
                    'message_type' => SystemMessageType::NOTIFY
                ]);

                $casts = [];
                foreach ($order->casts as $cast) {
                    $involedUsers[] = $cast;
                    $casts[] = $cast->id;

                    $messageForGuest = $cast->nickname . 'の解散予定時刻まで残り10分です。';
                    $roomMessage = $room->messages()->create([
                        'user_id' => 1,
                        'type' => MessageType::SYSTEM,
                        'message' => $messageForGuest,
                        'message_type' => SystemMessageType::NOTIFY
                    ]);
                    $roomMessage->recipients()->attach($order->user_id, ['room_id' => $room->id]);
                    $order->user->notify(new TenMinBeforeOrderEnded($order, $cast));
                }

                $roomMessage->recipients()->attach($casts, ['room_id' => $room->id]);
                \Notification::send($order->casts, new RenewalReminderTenMinute($order));
            }
        }
    }
}
