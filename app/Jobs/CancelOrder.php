<?php

namespace App\Jobs;

use App\Enums\CastOrderStatus;
use App\Enums\OrderStatus;
use App\Order;
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

            $orderPoint = 0;
            $orderDuration = $this->order->duration * 60;
            $orderNightTime = $this->order->nightTime($orderStartDate->addMinutes($orderDuration));
            $orderAllowance = $this->order->allowance($orderNightTime);

            foreach ($casts as $cast) {
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
        }
    }
}
