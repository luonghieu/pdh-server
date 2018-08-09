<?php

namespace App\Console\Commands;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancelFeeSettlement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheers:cancel_fee_settlement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel fee settlement';

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

        $orders = Order::where('status', OrderStatus::CANCELED)
            ->whereNull('payment_status')
            ->where('canceled_at', '<=', $now->subHours(24))
            ->whereNotNull('total_point')
            ->get();

        foreach ($orders as $order) {
            try {
                \DB::beginTransaction();

                $order->settle();

                $order->payment_status = OrderPaymentStatus::CANCEL_FEE_PAYMENT_FINISHED;
                $order->paid_at = $now;

                $order->update();

                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                LogService::writeErrorLog($e);
            }
        }
    }
}
