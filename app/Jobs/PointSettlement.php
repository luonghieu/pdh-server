<?php

namespace App\Jobs;

use App\Enums\OrderPaymentStatus;
use App\Enums\PaymentRequestStatus;
use App\Enums\PointType;
use App\Enums\UserType;
use App\Jobs\PointSettlement;
use App\Order;
use App\Point;
use App\Services\LogService;
use App\Traits\DirectRoom;
use App\Transfer;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PointSettlement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DirectRoom;

    public $order;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $now = Carbon::now();
        try {
            \DB::beginTransaction();

            $this->order->settle();
            $this->order->paymentRequests()->update(['status' => PaymentRequestStatus::CLOSED]);

            $this->order->payment_status = OrderPaymentStatus::PAYMENT_FINISHED;
            $this->order->paid_at = $now;
            $this->order->update();

            $adminId = User::where('type', UserType::ADMIN)->first()->id;

            $order = $this->order->load('paymentRequests');

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

    private function createTransfer($order, $paymentRequest, $receiveCast)
    {
        $transfer = new Transfer;
        $transfer->order_id = $order->id;
        $transfer->user_id = $paymentRequest->cast_id;
        $transfer->amount = $receiveCast;
        $transfer->save();
    }

    private function createPoint($receive, $id, $order)
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
