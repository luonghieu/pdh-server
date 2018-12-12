<?php

namespace App\Console\Commands;

use App\Enums\OrderPaymentStatus;
use App\Enums\ProviderType;
use App\Jobs\PointSettlement;
use App\Order;
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

        $lineOrders = Order::where(function ($query) {
            $query->where('payment_status', OrderPaymentStatus::REQUESTING)
                ->orWhere('payment_status', OrderPaymentStatus::PAYMENT_FAILED);
        })
            ->where('payment_requested_at', '<=', $now->copy()->subHours(3))
            ->whereHas('user', function ($q) {
                $q->where(function ($sQ) {
                    $sQ->where('payment_suspended', false)
                        ->orWhere('payment_suspended', null);
                });
            })
            ->get();

        foreach ($lineOrders as $order) {
            PointSettlement::dispatchNow($order->id);
        }
    }
}
