<?php

namespace App\Jobs;

use App\Order;
use App\Enums\OrderStatus;
use Illuminate\Bus\Queueable;
use App\Enums\CastOrderStatus;
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
        }
    }
}
