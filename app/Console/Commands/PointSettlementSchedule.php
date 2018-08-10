<?php

namespace App\Console\Commands;

use App\Enums\OrderPaymentStatus;
use App\Enums\PaymentRequestStatus;
use App\Enums\PointType;
use App\Enums\UserType;
use App\Order;
use App\Point;
use App\Transfer;
use App\Services\LogService;
use App\User;
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

                $adminId = User::where('type', UserType::ADMIN)->first()->id;

                $order = $order->load('paymentRequests');

                $paymentRequests = $order->paymentRequests;

                $receiveAdmin = 0;
                foreach ($paymentRequests as $paymentRequest) {
                    $receiveCast = $paymentRequest->total_point * 0.8;
                    $receiveAdmin += $paymentRequest->total_point * 0.2;

                    $this->createTransfer($order, $paymentRequest, $receiveCast);

                    // receive cast
                    $this->createPoint($receiveCast, $paymentRequest->cast_id, $order);
                }

                // receive admin
                $this->createPoint($receiveAdmin, $adminId, $order);

                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                LogService::writeErrorLog($e);
            }
        }
    }

    public function createTransfer($order, $paymentRequest, $receiveCast)
    {
        $transfer = new Transfer;
        $transfer->order_id = $order->id;
        $transfer->user_id = $paymentRequest->cast_id;
        $transfer->amount = $receiveCast;
        $transfer->save();
    }

    public function createPoint($receive, $id, $order)
    {
        $user = User::find($id);

        $point = new Point;
        $point->point = $receive;
        $point->balance = $user->point + $receive;
        $point->user_id = $user->id;
        $point->order_id = $order->id;
        $point->type = PointType::RECEIVE;
        $point->status = true;
        $point->save();

        $user->point += $receive;
        $user->update();
    }
}
