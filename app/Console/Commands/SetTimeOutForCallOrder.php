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
        $now = Carbon::now();

        $orders = Order::where('status', OrderStatus::OPEN)
            ->whereIn('type', [OrderType::CALL, OrderType::NOMINATION])->get();

        foreach ($orders as $order) {
            $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $order->date . ' ' . $order->start_time);
            $createdAt = $order->created_at;
            $timeApply = $startTime->copy()->diffInMinutes($createdAt);

            if (($timeApply > 60)) {
                $timeout = $startTime->copy()->subMinute(30);
                if ($timeout < $now) {
                    $order->status = OrderStatus::TIMEOUT;
                    $order->canceled_at = now();
                    $order->save();
                }
            }

            if (($timeApply <= 60)) {
                $timeApplyHalf = $startTime->copy()->diffInMinutes($createdAt) / 2;
                $timeout = $startTime->copy()->subMinute($timeApplyHalf);
                if ($timeout < $now) {
                    $order->status = OrderStatus::TIMEOUT;
                    $order->canceled_at = now();
                    $order->save();
                }
            }
        }
    }
}
