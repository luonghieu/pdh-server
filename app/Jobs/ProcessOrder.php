<?php

namespace App\Jobs;

use App\Order;
use App\Enums\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessOrder implements ShouldQueue
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
        if ($this->order->status == OrderStatus::ACTIVE) {
            $startedCasts = $this->order->casts()->whereNotNull('started_at')->exists();

            if ($startedCasts) {
                $this->order->status = OrderStatus::PROCESSING;
                $this->order->actual_started_at = now();
                $this->order->save();
            }
        }
    }
}
