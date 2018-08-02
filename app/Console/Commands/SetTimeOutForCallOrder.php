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
        $time = Carbon::now()->format('Y-m-d H:i:s');

        Order::where('status', OrderStatus::OPEN)
            ->whereIn('type', [OrderType::CALL, OrderType::NOMINATION])
            ->whereRaw("(time_to_sec(timediff(concat_ws(' ',`date`,`start_time`), created_at))/60) > 60")
            ->whereRaw("(time_to_sec(timediff(concat_ws(' ',`date`,`start_time`), '$time'))/60) < 30")
            ->update(['status' => OrderStatus::TIMEOUT]);

        Order::where('status', OrderStatus::OPEN)
            ->whereIn('type', [OrderType::CALL, OrderType::NOMINATION])
            ->whereRaw("(time_to_sec(timediff(concat_ws(' ',`date`,`start_time`), created_at))/60) <= 60")
            ->whereRaw("(time_to_sec(timediff(concat_ws(' ',`date`,`start_time`), '$time'))/60) < ((time_to_sec(timediff(concat_ws(' ',`date`,`start_time`), created_at))/60) /2)")
            ->update(['status' => OrderStatus::TIMEOUT]);
    }
}
