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

        $orders = Order::where('payment_status', OrderPaymentStatus::REQUESTING)
            ->where('payment_requested_at', '<=', $now->copy()->subHours(24))
            ->whereHas('user', function($q) {
                $q->where('provider', '<>', ProviderType::LINE)
                    ->orWhere('provider', null);
            })
            ->get();
        foreach ($orders as $order) {
            PointSettlement::dispatchNow($order->id);
        }

        $lineOrders = Order::where('payment_status', OrderPaymentStatus::REQUESTING)
            ->where('payment_requested_at', '<=', $now->copy()->subHours(3))
            ->whereHas('user', function($q) {
                $q->where('provider', ProviderType::LINE);
            })
            ->get();

        foreach ($lineOrders as $order) {
            PointSettlement::dispatchNow($order->id);
        }

    }
}
