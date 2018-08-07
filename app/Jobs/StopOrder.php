<?php

namespace App\Jobs;

use App\Notifications\OrderCompleted;
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
    protected $cast;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     * @param null $cast
     */
    public function __construct(Order $order, $cast = null)
    {
        $this->order = $order;
        $this->cast = $cast;
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

        if ($this->cast) {
            $this->order->user->notify(new OrderCompleted($this->order, $this->cast));
        }
    }
}
