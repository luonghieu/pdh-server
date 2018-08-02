<?php

namespace App\Jobs;

use App\Order;
use App\Enums\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StopOrder implements ShouldQueue
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
        if ($this->order->status == OrderStatus::PROCESSING) {
            $stoppedCasts = $this->order->casts()->whereNotNull('stopped_at')->count();

            if ($this->order->total_cast == $stoppedCasts) {
                $this->order->status = OrderStatus::DONE;
                $this->order->actual_ended_at = now();
                $this->order->save();
            }
        }
    }
}
