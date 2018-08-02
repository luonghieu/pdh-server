<?php

namespace App\Http\Controllers\Api\Cast;

use App\Enums\CastOrderStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentRequestStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\PaymentRequestResource;
use App\Order;
use App\PaymentRequest;
use App\Services\LogService;
use Illuminate\Http\Request;

class PaymentRequestController extends ApiController
{
    public function payment(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->respondErrorMessage(trans('messages.order_not_found'), 404);
        }

        $user = $this->guard()->user();
        $cast = $order->casts()
            ->where([
                ['cast_order.status', CastOrderStatus::DONE],
                ['user_id', $user->id],
                ['order_id', $id],
            ])
            ->whereNotNull('stopped_at')->get();

        if (!$cast || OrderStatus::DONE != $order->status) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        $paymentRequest = PaymentRequest::where([
            ['order_id', $id],
            ['cast_id', $user->id],
        ])->with('cast', 'guest', 'order.casts')->first();

        return $this->respondWithData(PaymentRequestResource::make($paymentRequest));
    }

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
                $extraPoint = $order->extraPoint($cast, $request->extra_time);
                $totalPoint = $paymentRequest->order_point + $paymentRequest->allowance_point + $paymentRequest->fee_point + $extraPoint;

                $paymentRequest->extra_time = $request->extra_time;
                $paymentRequest->extra_point = $extraPoint;
                $paymentRequest->total_point = $totalPoint;
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

    public function getPaymentHistory(Request $request)
    {
        $user = $this->guard()->user();

        $paymentRequests = PaymentRequest::where('cast_id', $user->id)->with('cast', 'guest', 'order.casts');

        $nickName = $request->nickname;
        if ($nickName) {
            $paymentRequests->whereHas('guest', function ($query) use ($nickName) {
                $query->where('users.nickname', 'like', "%$nickName%");
            });
        }
        $paymentRequests = $paymentRequests->latest()->paginate($request->per_page)->appends($request->query());

        return $this->respondWithData(PaymentRequestResource::collection($paymentRequests));
    }
}
