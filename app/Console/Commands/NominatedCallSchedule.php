<?php

namespace App\Console\Commands;

use App\Enums\CastOrderStatus;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Notifications\CastDenyOrders;
use App\Order;
use App\Services\LogService;
use Carbon\Carbon;
use DB;
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
            ->where(function ($query) {
                $query->where('type', OrderType::NOMINATED_CALL)
                    ->orWhere(function ($query) {
                        $query->where('type', OrderType::HYBRID)
                            ->where('is_changed', false);
                    });
            })
            ->where('created_at', '<=', Carbon::now()->subMinutes(5));

        foreach ($orders->cursor() as $order) {
            $nominees = $order->nominees()
                ->where('cast_order.status', CastOrderStatus::OPEN)
                ->get();
            $owner = $order->user;
            try {
                DB::beginTransaction();

                foreach ($nominees as $nominee) {
                    $order->nominees()->updateExistingPivot(
                        $nominee->id,
                        [
                            'status' => CastOrderStatus::TIMEOUT,
                            'canceled_at' => now(),
                            'deleted_at' => now(),
                        ],
                        false
                    );
                    $owner->notify(new CastDenyOrders($order, $nominee));
                }

                $castsCount = $order->casts->count();

                if (($castsCount > 0) && ($order->total_cast != $castsCount)) {
                    $order->update([
                        'type' => OrderType::HYBRID,
                        'is_changed' => true,
                    ]);
                } else {
                    $order->update([
                        'type' => OrderType::CALL,
                        'is_changed' => true,
                    ]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                LogService::writeErrorLog($e);
            }
        }
    }
}
