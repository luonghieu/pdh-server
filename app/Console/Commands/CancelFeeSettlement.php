<?php

namespace App\Console\Commands;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentRequestStatus;
use App\Enums\PointType;
use App\Enums\ProviderType;
use App\Enums\UserType;
use App\Notifications\AutoChargeFailed;
use App\Notifications\AutoChargeFailedWorkchatNotify;
use App\Order;
use App\PaymentRequest;
use App\Point;
use App\Services\LogService;
use App\Transfer;
use App\User;
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
            ->where('canceled_at', '<=', $now->copy()->subHours(24))
            ->where('cancel_fee_percent', '>', 0)
            ->whereHas('user', function ($q) {
                $q->where('provider', '<>', ProviderType::LINE)
                    ->orWhere('provider', null);
            })
            ->get();

        foreach ($orders as $order) {
            $this->processPayment($order, $now);
        }

        $lineOrders = Order::where('status', OrderStatus::CANCELED)
            ->whereNull('payment_status')
            ->where('canceled_at', '<=', $now->copy()->subHours(3))
            ->where('cancel_fee_percent', '>', 0)->whereHas('user', function ($q) {
            $q->where('provider', ProviderType::LINE);
        })->get();

        foreach ($lineOrders as $order) {
            $this->processPayment($order, $now);
        }
    }

    public function processPayment($order, $time)
    {
        try {
            \DB::beginTransaction();
            $order->settle();
            foreach ($order->canceledCasts as $cast) {
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
            $order->paid_at = $time;
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
            if ($e->getMessage() == 'Auto charge failed') {
                $user = $order->user;
                $user->suspendPayment();
                if (!$order->send_warning) {
                    $order->user->notify(new AutoChargeFailedWorkchatNotify($this->order));
                    if (ProviderType::LINE == $user->provider) {
                        $order->user->notify(new AutoChargeFailed($order));
                    }

                    $order->send_warning = true;
                    $order->payment_status = OrderPaymentStatus::PAYMENT_FAILED;
                    $order->save();
                }
            }
            LogService::writeErrorLog($e);
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
