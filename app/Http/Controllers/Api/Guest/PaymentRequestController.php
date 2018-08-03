<?php

namespace App\Http\Controllers\Api\Guest;

use App\Enums\OrderPaymentStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;

class PaymentRequestController extends ApiController
{
    public function payment(Request $request, $id)
    {
        $user = $this->guard()->user();
        $order = $user->orders()->where('orders.id', $id)->first();

        if (!$order) {
            return $this->respondErrorMessage(trans('messages.order_not_found'), 404);
        }

        if (OrderPaymentStatus::REQUESTING != $order->payment_status) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        try {
            $order->payment_status = OrderPaymentStatus::EDIT_REQUESTING;
            $order->save();

            return $this->respondWithData(OrderResource::make($order));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }
}
