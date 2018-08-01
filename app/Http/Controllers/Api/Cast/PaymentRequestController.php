<?php

namespace App\Http\Controllers\Api\Cast;

use App\Enums\CastOrderStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentRequestStatus;
use App\Http\Controllers\Api\ApiController;
use App\Order;
use App\Services\LogService;
use Illuminate\Http\Request;

class PaymentRequestController extends ApiController
{
    public function createPayment(Request $request, $id)
    {
        $rules = [
            'extra_time' => 'numeric',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();

        $order = Order::find($id);

        if (!$order) {
            return $this->respondErrorMessage(trans('messages.order_not_found'), 404);
        }

        $cast = $order->casts()
            ->where([
                ['user_id', $user->id],
                ['order_id', $id],
                ['cast_order.status', CastOrderStatus::DONE],
            ])
            ->with('castClass')->first();

        $paymentRequest = $order->paymentRequests()->where([
            ['cast_id', $user->id],
            ['order_id', $id],
            ['payment_requests.status', PaymentRequestStatus::OPEN],
        ])->first();

        if (OrderStatus::DONE != $order->status || !$cast || !$paymentRequest) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        try {
            if ($request->extra_time) {
                $paymentRequest->extra_time = $request->extra_time;
                $extraPoint = $order->extraPoint($cast, $request->extra_time);
                $paymentRequest->extra_point = $extraPoint;
                $paymentRequest->total_point = $paymentRequest->total_point + $extraPoint;
                $paymentRequest->status = PaymentRequestStatus::UPDATED;
            } else {
                $paymentRequest->status = PaymentRequestStatus::REQUESTED;
            }

            $paymentRequest->save();

            return $this->respondWithNoData(trans('messages.create_payment_request_success'));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }
}
