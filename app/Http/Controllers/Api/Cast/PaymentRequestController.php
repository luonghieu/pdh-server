<?php

namespace App\Http\Controllers\Api\Cast;

use App\Enums\CastOrderStatus;
use App\Enums\OrderStatus;
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

        $order = Order::find($id);

        if (!$order) {
            return $this->respondErrorMessage(trans('messages.order_not_found'), 404);
        }

        if (OrderStatus::DONE != $order->status) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        $user = $this->guard()->user();

        $cast = $order->casts()
            ->where([
                ['cast_order.status', CastOrderStatus::DONE],
                ['user_id', $user->id],
                ['order_id', $id],
            ])->with('castClass')
            ->whereNotNull('stopped_at')->first();

        $paymentRequestExist = PaymentRequest::where('cast_id', $user->id)->where('order_id', $id)->exists();

        if ($paymentRequestExist || !$cast) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        try {
            $totalPoint = ($cast->pivot->order_point + $cast->pivot->allowance_point + $cast->pivot->fee_point);
            $paymentRequest = new PaymentRequest;
            $paymentRequest->cast_id = $user->id;
            $paymentRequest->order_id = $id;
            $paymentRequest->cast_id = $user->id;
            $paymentRequest->guest_id = $order->user_id;
            $paymentRequest->order_id = $id;
            $paymentRequest->order_time = $cast->pivot->order_time;
            $paymentRequest->order_point = $cast->pivot->order_point;
            $paymentRequest->allowance_point = $cast->pivot->allowance_point;
            $paymentRequest->fee_point = $cast->pivot->fee_point;

            if ($request->extra_time) {
                $paymentRequest->extra_time = $request->extra_time;
                $extraPoint = $order->extraPoint($cast, $request->extra_time);
            } else {
                $paymentRequest->extra_time = $cast->pivot->extra_time;
                $extraPoint = $order->extraPoint($cast, $cast->pivot->extra_time);
            }

            $paymentRequest->extra_point = $extraPoint;

            $paymentRequest->total_point = $totalPoint + $extraPoint;

            $paymentRequest->save();

            return $this->respondWithNoData(trans('messages.create_paymentRequest_success'));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }
}
