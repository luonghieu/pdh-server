<?php

namespace App\Console\Commands;

use App\User;
use App\Order;
use App\Point;
use App\Transfer;
use Carbon\Carbon;
use App\Enums\UserType;
use App\PaymentRequest;
use App\Enums\PointType;
use App\Enums\OrderStatus;
use Illuminate\Console\Command;
use App\Enums\OrderPaymentStatus;
use App\Enums\PaymentRequestStatus;

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

                foreach ($order->casts as $cast) {
                    $paymentRequest = new PaymentRequest;
                    $paymentRequest->cast_id = $cast->id;
                    $paymentRequest->guest_id = $order->user_id;
                    $paymentRequest->order_id = $order->id;
                    $paymentRequest->order_time = (60 * $order->duration);
                    $paymentRequest->order_point = 0;
                    $paymentRequest->allowance_point = 0;
                    $paymentRequest->fee_point = 0;
                    $paymentRequest->extra_time = 0;
                    $paymentRequest->old_extra_time = 0;
                    $paymentRequest->extra_point = 0;
                    $paymentRequest->total_point = ($cast->pivot->temp_point * $order->cancel_fee_percent) / 100;
                    $paymentRequest->status = PaymentRequestStatus::CLOSED;
                    $paymentRequest->save();
                }

                $order->payment_status = OrderPaymentStatus::CANCEL_FEE_PAYMENT_FINISHED;
                $order->paid_at = $now;
                $order->update();

                $adminId = User::where('type', UserType::ADMIN)->first()->id;

                $order = $order->load('paymentRequests');

                $paymentRequests = $order->paymentRequests;

                $receiveAdmin = 0;
                $castPercent = config('common.cast_percent');

                foreach ($paymentRequests as $paymentRequest) {
                    $receiveCast = $paymentRequest->total_point * $castPercent;
                    $receiveAdmin += $paymentRequest->total_point * (1 - $castPercent);

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
