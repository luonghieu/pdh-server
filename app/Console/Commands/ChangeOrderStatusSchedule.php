<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ChangeOrderStatusSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheers:change_order_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change order status to processing at start time';

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
        $currentTime = Carbon::now()->format('H:i');
        $currentDate = Carbon::now()->format('Y-m-d');

        try {
            $orders = Order::where('status', OrderStatus::ACTIVE)
                ->whereDate('date', $currentDate)
                ->whereRaw('TIME_FORMAT(start_time, "%H:%i ") = "' . $currentTime . '"')
                ->update(['status' => OrderStatus::PROCESSING]);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
        }
    }
}
