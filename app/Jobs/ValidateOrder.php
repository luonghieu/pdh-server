<?php

namespace App\Jobs;

use App\Room;
use App\Order;
use App\Enums\RoomType;
use App\Enums\OrderType;
use App\Enums\MessageType;
use App\Enums\OrderStatus;
use App\Traits\DirectRoom;
use App\Enums\CastOrderType;
use App\Services\LogService;
use Illuminate\Bus\Queueable;
use App\Enums\CastOrderStatus;
use Illuminate\Support\Carbon;
use App\Enums\SystemMessageType;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\ApproveNominatedOrders;

class ValidateOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DirectRoom;

    public $order;

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
        $acceptByCast = $this->order->casts->count();

        if ($this->order->total_cast == $acceptByCast) {
            try {
                \DB::beginTransaction();

                if ($this->order->total_cast > 1) {
                    $room = new Room;
                    $room->order_id = $this->order->id;
                    $room->owner_id = $this->order->user_id;
                    $room->type = RoomType::GROUP;
                    $room->save();

                    $data = [$this->order->user_id];
                    foreach ($this->order->casts as $cast) {
                        $data = array_merge($data, [$cast->pivot->user_id]);
                    }

                    $room->users()->attach($data);
                } else {
                    $ownerId = $this->order->user_id;
                    $userId = $this->order->casts()->first()->id;

                    $room = $this->createDirectRoom($ownerId, $userId);
                }

                // activate order
                $this->order->status = OrderStatus::ACTIVE;
                $this->order->room_id = $room->id;
                $this->order->update();

                $involvedUsers = [$this->order->user];
                foreach ($this->order->casts as $cast) {
                    $involvedUsers[] = $cast;
                }

                $this->sendNotification($involvedUsers);

                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                LogService::writeErrorLog($e);
            }
        } else {
            $numOfReply = $this->order->nominees()
                ->whereNotNull('cast_order.status')
                ->where('cast_order.status', '!=', 0)
                ->count();

            $numOfNominee = $this->order->nominees()->count();

            if ($numOfReply == $numOfNominee && $numOfNominee > 0) {
                if ($this->order->total_cast == 1) {
                    $this->order->status = OrderStatus::DENIED;
                } else {
                    $this->order->type = OrderType::CALL;
                }

                $this->order->update();
            }
        }
    }

    private function sendNotification($users)
    {
        $room = $this->order->room;
        $startTime = Carbon::parse($this->order->date . ' ' . $this->order->start_time);
        $message = '\\\\ おめでとうございます！マッチングが確定しました♪ //'
            . PHP_EOL . PHP_EOL . '- ご予約内容 - '
            . PHP_EOL . '場所：' . $this->order->address
            . PHP_EOL . '合流予定時間：'. $startTime->format('H:i') .'～'
            . PHP_EOL . PHP_EOL . 'ゲストの方はキャストに来て欲しい場所の詳細をお伝えください。'
            . PHP_EOL . '尚、ご不明点がある場合は運営までお問い合わせください。'
            . PHP_EOL . PHP_EOL . 'それでは素敵な時間をお楽しみください♪';

        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'system_type' => SystemMessageType::NORMAL,
            'message' => $message
        ]);

        $userIds = [];
        foreach ($users as $user) {
            $userIds[] = $user->id;
        }

        $roomMessage->recipients()->attach($userIds, ['room_id' => $room->id]);

        \Notification::send($users, new ApproveNominatedOrders($this->order));
    }
}
