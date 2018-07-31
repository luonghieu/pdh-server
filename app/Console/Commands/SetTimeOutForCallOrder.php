<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetTimeOutForCallOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheers:set_timeout_for_call_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Any order call that exceeds the allowable time will become a timeout';

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
        $today = Carbon::today();
        $now = now()->format('H:i:s');

        Order::whereDate('created_at', $today)
            ->where('status', OrderStatus::OPEN)
            ->where('type', OrderType::CALL)
            ->whereRaw("(time_to_sec(timediff(time(start_time), time(created_at))) / 60) > 60")
            ->whereRaw("(time_to_sec(timediff(time(start_time), '$now')) / 60) < 30")
            ->update(['status' => OrderStatus::TIMEOUT]);

        Order::whereDate('created_at', $today)
            ->where('status', OrderStatus::OPEN)
            ->where('type', OrderType::CALL)
            ->whereRaw("(time_to_sec(timediff(time(start_time), time(created_at))) / 60) <= 60")
            ->whereRaw("((time_to_sec(timediff(time(created_at), time(start_time))) / 60) /2) < '$now'")
            ->update(['status' => OrderStatus::TIMEOUT]);
    }
}
