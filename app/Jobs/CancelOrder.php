<?php

namespace App\Jobs;

use App\Enums\CastOrderStatus;
use App\Enums\MessageType;
use App\Enums\OrderStatus;
use App\Enums\RoomType;
use App\Enums\SystemMessageType;
use App\Notifications\CancelOrderFromGuest;
use App\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CancelOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->order->status == OrderStatus::CANCELED) {
            $castIds = $this->order->castOrder()
                ->pluck('cast_order.user_id')
                ->toArray();

            foreach ($castIds as $id) {
                $this->order->castOrder()->updateExistingPivot(
                    $id,
                    [
                        'status' => CastOrderStatus::CANCELED,
                        'canceled_at' => $this->order->canceled_at
                    ],
                    false
                );
            }

            $orderStartDate = Carbon::parse($this->order->date)->startOfDay();
            $orderCancelDate = Carbon::parse($this->order->canceled_at)->startOfDay();
            $casts = $this->order->casts;
            $involvedUsers = [];

            $orderPoint = 0;
            $orderDuration = $this->order->duration * 60;
            $orderNightTime = $this->order->nightTime($orderStartDate->addMinutes($orderDuration));
            $orderAllowance = $this->order->allowance($orderNightTime);

            foreach ($casts as $cast) {
                $involvedUsers[] = $cast;
                $orderFee = $this->order->orderFee($cast, 0);
                $orderPoint += $this->order->orderPoint($cast) + $orderAllowance + $orderFee;
            }

            $percent = 0;
            if ($orderCancelDate->diffInDays($orderStartDate) <= 7) {
                $percent = 0.3;
            }

            if ($orderCancelDate->diffInDays($orderStartDate) == 1) {
                $percent = 0.5;
            }

            if ($orderCancelDate->diffInDays($orderStartDate) == 0) {
                $percent = 1;
            }

            $cancelFee = $orderPoint * $percent;

            $this->order->total_point = $cancelFee;
            $this->order->cancel_fee_percent = $percent * 100;
            $this->order->save();

            $this->sendPushNotification($involvedUsers, $orderPoint);
        }
    }

    private function sendPushNotification($users, $orderPoint)
    {
        $castIds = [];
        foreach ($users as $user) {
            if ($user->id == $this->order->user_id) {
                continue;
            }
            $castIds[] = $user->id;
        }

        $room = $this->order->room;
        $message = '予約がキャンセルされました。';
        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $message,
            'system_type' => SystemMessageType::NOTIFY,
        ]);
        $roomMessage->recipients()->attach($castIds, ['room_id' => $room->id]);

        // Send message to owner private room.
        $owner = $this->order->user;
        $ownerRoom = $owner->rooms()
            ->where('rooms.type', RoomType::SYSTEM)
            ->where('rooms.is_active', true)->first();
        $ownerMessage = '予約のキャンセルを承りました。'
            . PHP_EOL . '--------------------------------------------------'
            . PHP_EOL . '- キャンセル内容 -'
            . PHP_EOL . '日時：' . Carbon::parse($this->order->date . ' ' . $this->order->start_time)->format('Y/m/d H:i') . '~'
            . PHP_EOL . '時間：' . $this->order->duration . '時間'
            . PHP_EOL . 'クラス：' . $this->order->castClass->name
            . PHP_EOL . '人数：' . $this->order->total_cast . '人'
            . PHP_EOL . '場所：' . $this->order->address
            . PHP_EOL . '予定合計ポイント：' . number_format($orderPoint) . ' Point'
            . PHP_EOL . '--------------------------------------------------'
            . PHP_EOL . PHP_EOL . 'キャンセル規定は以下の通りとなっています。'
            . PHP_EOL . '該当期間内のキャンセルについては、キャンセル料が決済されます。'
            . PHP_EOL . '当日：予約時の金額100%'
            . PHP_EOL . '1日前：予約時の金額50%'
            . PHP_EOL . '2日前〜７日前：予約時の金額30%'
            . PHP_EOL . PHP_EOL . '※キャスト都合によるキャンセルの場合、キャンセル料金はいただきません。'
            . PHP_EOL . '※ご不明点がある場合は、こちらのチャットにて、ご返信くださいませ。';

        $ownerRoomMessage = $ownerRoom->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $ownerMessage,
            'system_type' => SystemMessageType::NORMAL,
        ]);
        $ownerRoomMessage->recipients()->attach($owner->id, ['room_id' => $ownerRoom->id]);

        \Notification::send($users, new CancelOrderFromGuest($this->order, $orderPoint));
    }
}
