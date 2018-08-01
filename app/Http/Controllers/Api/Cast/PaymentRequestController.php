<?php

namespace App\Http\Controllers\Api\Cast;

use App\Enums\PaymentRequestStatus;
use App\Http\Controllers\Api\ApiController;
use App\Order;
use App\PaymentRequest;
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

        $paymentRequest = PaymentRequest::find($id);

        $order = Order::find($paymentRequest->order_id);
        if (!$order) {
            return $this->respondErrorMessage(trans('messages.order_not_found'), 404);
        }

        $cast = $order->casts()->where('user_id', $user->id)->with('castClass')->first();

        if (PaymentRequestStatus::OPEN != $paymentRequest->status || !$cast) {
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

            return $this->respondWithNoData(trans('messages.create_paymentRequest_success'));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }
}
