<?php

namespace App\Observers;

use App\PaymentRequest;
use App\Enums\OrderPaymentStatus;
use App\Enums\PaymentRequestStatus;

class PaymentRequestObserver
{
    /**
     * Handle to the payment request "created" event.
     *
     * @param  \App\PaymentRequest  $paymentRequest
     * @return void
     */
    public function created(PaymentRequest $paymentRequest)
    {
        //
    }

    /**
     * Handle the payment request "updated" event.
     *
     * @param  \App\PaymentRequest  $paymentRequest
     * @return void
     */
    public function updated(PaymentRequest $paymentRequest)
    {
        if ($paymentRequest->getOriginal('status') != $paymentRequest->status) {
            $status = $paymentRequest->status;
            $order = $paymentRequest->order;

            // check to update order payment status
            $requestedStatuses = [
                PaymentRequestStatus::REQUESTED,
                PaymentRequestStatus::UPDATED
            ];

            if (in_array($status, $requestedStatuses)) {
                $requestedCount = $order->paymentRequests()->whereIn('status', $requestedStatuses)->count();

                if ($order->total_cast > 1) {
                    if ($requestedCount != $order->total_cast) {
                        if ($order->payment_status != OrderPaymentStatus::WAITING) {
                            $order->payment_status = OrderPaymentStatus::WAITING;
                            $order->save();
                        }
                    } else {
                        $order->payment_status = OrderPaymentStatus::REQUESTING;
                        $order->payment_requested_at = now();
                        $order->save();
                    }
                } else {
                    $order->payment_status = OrderPaymentStatus::REQUESTING;
                    $order->payment_requested_at = now();
                    $order->save();
                }
            }
        }
    }

    /**
     * Handle the payment request "deleted" event.
     *
     * @param  \App\PaymentRequest  $paymentRequest
     * @return void
     */
    public function deleted(PaymentRequest $paymentRequest)
    {
        //
    }
}
