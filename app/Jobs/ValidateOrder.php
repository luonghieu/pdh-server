<?php

namespace App\Jobs;

use App\Room;
use App\Order;
use App\Enums\RoomType;
use App\Enums\OrderType;
use App\Enums\MessageType;
use App\Enums\OrderStatus;
use App\Traits\DirectRoom;
use App\Services\LogService;
use Illuminate\Bus\Queueable;
use App\Enums\CastOrderStatus;
use Illuminate\Support\Carbon;
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
        $accept = CastOrderStatus::ACCEPTED;

        $acceptByCast = $this->order->casts->count();

        $orderType = $this->order->type;

        if ($this->order->total_cast == $acceptByCast) {
            $this->order->status = OrderStatus::ACTIVE;
            $this->order->update();

            // Create room chat
            \DB::beginTransaction();
            try {
                if ($orderType == OrderType::NOMINATED_CALL || $orderType == OrderType::CALL) {
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

                        $this->createDirectRoom($ownerId, $userId);
                    }
                }

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
            $numOfReply = \DB::table('cast_order')
                    ->where('order_id', '=', $this->order->id)
                    ->whereNotNull('accepted_at')
                    ->orWhereNotNull('canceled_at')
                    ->count();

            $numOfNominee = $this->order->nominees()->count();

            if ($numOfReply == $numOfNominee && $numOfNominee > 0) {
                // When Cast deny order,
                // If OrderType is NOMINATION (1-1) then update status
                $isNomination = ($orderType == OrderType::NOMINATION) ? 1 : 0;
                if ($isNomination) {
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
        if ($this->order->total_cast == 1) {
            $room = Room::find($this->order->room_id);
        } else {
            $room = $this->order->room;
        }


        $startTime = Carbon::parse($this->order->date . ' ' . $this->order->start_time);
        $message = '\\\\ おめでとうございます！マッチングが確定しました♪ //'
            .'\n \n- ご予約内容 - '
            .'\n 場所：' . $this->order->address
            .'\n 合流予定時間：'. $startTime->format('H:i') .'～'
            .'\n \n ゲストの方はキャストに来て欲しい場所の詳細をお伝えください。'
            .'\n 尚、ご不明点がある場合は運営までお問い合わせください。'
            .'\n \n それでは素敵な時間をお楽しみください♪';

        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
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
