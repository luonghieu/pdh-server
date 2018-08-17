<?php

namespace App\Jobs;

use App\Enums\UserType;
use App\Notifications\CastAcceptNominationOrders;
use App\Notifications\CastApplyOrders;
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
        $casts = $this->order->casts()->get();

        if ($this->order->total_cast == $casts->count()) {
            try {
                \DB::beginTransaction();
                if ($this->order->total_cast > 1) {
                    $room = new Room;
                    $room->order_id = $this->order->id;
                    $room->owner_id = $this->order->user_id;
                    $room->type = RoomType::GROUP;
                    $room->save();

                    $data = [$this->order->user_id];
                    foreach ($casts as $cast) {
                        $data = array_merge($data, [$cast->pivot->user_id]);
                    }

                    $room->users()->attach($data);
                } else {
                    $ownerId = $this->order->user_id;
                    $userId = $casts->first()->id;

                    $room = $this->createDirectRoom($ownerId, $userId);
                }

                // activate order
                $this->order->status = OrderStatus::ACTIVE;
                $this->order->room_id = $room->id;
                $this->order->update();

                $isHybrid = false;
                if ($this->order->type == OrderType::CALL || $this->order->type == OrderType::HYBRID) {
                    $isHybrid = true;
                }

                $involvedUsers = [$this->order->user];
                foreach ($casts as $cast) {
                    $involvedUsers[] = $cast;

                    if ($isHybrid) {
                        $cast->notify(new CastApplyOrders($this->order, $cast->pivot->temp_point));
                    }
                }

                $this->sendNotification($involvedUsers);

                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                LogService::writeErrorLog($e);
            }
        } else {
            $repliesCount = $this->order->nominees()
                ->whereNotNull('cast_order.status')
                ->where('cast_order.status', '!=', CastOrderStatus::OPEN)
                ->count();

            $nomineesCount = $this->order->nominees()->count();

            if ($repliesCount == $nomineesCount && $nomineesCount > 0) {
                if ($this->order->type == OrderType::NOMINATION) {
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
        if ($this->order->type != OrderType::NOMINATION) {
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
        } else {
            $room = $this->order->room;
            $userIds = [];
            foreach ($users as $user) {
                $userIds[] = $user->id;
            }

            $firstMessage = 'マッチングが確定しました。';
            $roomMessage = $room->messages()->create([
                'user_id' => 1,
                'type' => MessageType::SYSTEM,
                'system_type' => SystemMessageType::NOTIFY,
                'message' => $firstMessage
            ]);
            $roomMessage->recipients()->attach($userIds, ['room_id' => $room->id]);

            $secondMessage = 'マッチング確定おめでとうございます♪'
                . PHP_EOL . '合流後はタイマーで時間計測を行い、解散予定の10分前には通知が届きます。'
                . PHP_EOL . '※解散予定時刻後は自動で延長されます。'
                . PHP_EOL . PHP_EOL . 'その他ご不明点がある場合は運営までお問い合わせください。'
                . PHP_EOL . PHP_EOL . 'それでは素敵な時間をお楽しみください♪';
            $roomMessage = $room->messages()->create([
                'user_id' => 1,
                'type' => MessageType::SYSTEM,
                'system_type' => SystemMessageType::NORMAL,
                'message' => $secondMessage
            ]);
            $roomMessage->recipients()->attach($userIds, ['room_id' => $room->id]);

            \Notification::send($users, new CastAcceptNominationOrders($this->order));
        }
    }
}
