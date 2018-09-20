<?php

namespace App\Console\Commands;

use App\Notifications\CallOrdersTimeOut;
use App\Order;
use App\User;
use Carbon\Carbon;
use App\Enums\OrderType;
use App\Enums\OrderStatus;
use App\Enums\CastOrderStatus;
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

        $validOrderTypes = [
            OrderType::CALL,
            OrderType::NOMINATION,
            OrderType::HYBRID,
        ];

        $orders = Order::where('status', OrderStatus::OPEN)
            ->whereIn('type', $validOrderTypes)->get();

        foreach ($orders as $order) {
            $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $order->date . ' ' . $order->start_time);
            $createdAt = $order->created_at;
            $timeApply = $startTime->copy()->diffInMinutes($createdAt);

            if (($timeApply > 60)) {
                $timeout = $startTime->copy()->subMinute(30);
                if ($timeout < $now) {
                    $this->setTimeoutForOrder($order);
                }
            }

            if (($timeApply <= 60)) {
                $timeApplyHalf = $startTime->copy()->diffInMinutes($createdAt) / 2;
                $timeout = $startTime->copy()->subMinute($timeApplyHalf);
                if ($timeout < $now) {
                    $this->setTimeoutForOrder($order);
                }
            }
        }
    }

    protected function setTimeoutForOrder(Order $order)
    {
        $order->status = OrderStatus::TIMEOUT;
        $order->canceled_at = now();
        $order->save();

        $castIds = $order->castOrder()
            ->pluck('cast_order.user_id')
            ->toArray();

        $user = User::find($order->user_id);
        $user->notify(new CallOrdersTimeOut($order));

        foreach ($castIds as $id) {
            $order->castOrder()->updateExistingPivot(
                $id,
                [
                    'status' => CastOrderStatus::TIMEOUT,
                    'canceled_at' => now()
                ],
                false
            );
        }
    }
}
