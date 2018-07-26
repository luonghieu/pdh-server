<?php

namespace App\Jobs;

use App\Enums\CastOrderStatus;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\RoomType;
use App\Order;
use App\Room;
use App\Services\LogService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ValidateOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;
    /**
     * Create a new job instance.
     *
     * @return void
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

        if ($this->order->total_cast == $acceptByCast) {
            $this->order->status = OrderStatus::ACTIVE;
            $this->order->update();

            // Create room chat
            \DB::beginTransaction();
            try {
                $room = new Room;

                $room->order_id = $this->order->id;
                $room->owner_id = $this->order->user_id;
                $room->type = RoomType::GROUP;
                $room->save();

                $data = [];
                foreach ($this->order->casts as $cast) {
                    $data = array_merge($data, [$cast->pivot->user_id]);
                }

                $room->users()->attach($data);

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
                $isNomination = ($this->order->type == OrderType::NOMINATION) ? 1 : 0;
                if ($isNomination) {
                    $this->order->status = OrderStatus::DENIED;
                } else {
                    $this->order->type = OrderType::CALL;
                }

                $this->order->update();
            }
        }
    }
}
