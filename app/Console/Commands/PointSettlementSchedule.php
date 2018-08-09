<?php

namespace App\Console\Commands;

use App\Enums\OrderPaymentStatus;
use App\Enums\PaymentRequestStatus;
use App\Order;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PointSettlementSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheers:point_settlement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Point settlement';

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

        $orders = Order::where('payment_status', OrderPaymentStatus::REQUESTING)
            ->where('payment_requested_at', '<=', $now->subHours(24))
            ->get();

        foreach ($orders as $order) {
            try {
                \DB::beginTransaction();

                $order->settle();

                $order->paymentRequests()->update(['status' => PaymentRequestStatus::CLOSED]);

                $order->payment_status = OrderPaymentStatus::PAYMENT_FINISHED;
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
