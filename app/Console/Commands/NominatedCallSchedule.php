<?php

namespace App\Console\Commands;

use App\Enums\CastOrderStatus;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NominatedCallSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheers:update_nominated_call';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update nominated call';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orders = Order::where('status', OrderStatus::OPEN)
            ->where('type', OrderType::NOMINATED_CALL)
            ->where('created_at', '<=', Carbon::now()->subMinutes(5));

        foreach ($orders->cursor() as $order) {
            $order->nominees()
                ->where('cast_order.status', CastOrderStatus::OPEN)
                ->forceDelete();

            $order->update(['type' => OrderType::CALL]);
        }
    }
}
