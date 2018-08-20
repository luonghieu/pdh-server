<?php

namespace App\Http\Controllers\Api\Cast;

use App\Enums\CastOrderStatus;
use App\Enums\PaymentRequestStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\PaymentRequestResource;
use App\Order;
use App\PaymentRequest;
use App\Services\LogService;
use Carbon\Carbon;
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

        if (!$cast) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        $paymentRequest = PaymentRequest::where([
            ['order_id', $id],
            ['cast_id', $user->id],
        ])->with('order.casts')->first();

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

        if (!$cast || !$paymentRequest) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        try {
            if ($request->extra_time) {
                $castStartTime = Carbon::parse($cast->pivot->started_at);
                $stoppedAt = $castStartTime->copy()->addMinutes($order->duration * 60)->addMinutes($request->extra_time);

                $extraPoint = $order->extraPoint($cast, $request->extra_time);
                $feePoint = $order->orderFee($cast, $castStartTime, $stoppedAt);

                $nightTime = $order->nightTime($stoppedAt);
                $allowance = $order->allowance($nightTime);
                $totalPoint = $paymentRequest->order_point + $allowance + $feePoint + $extraPoint;
                $paymentRequest->allowance_point = $allowance;
                $paymentRequest->extra_time = $request->extra_time;
                $paymentRequest->extra_point = $extraPoint;
                $paymentRequest->fee_point = $feePoint;
                $paymentRequest->total_point = $totalPoint;
                $paymentRequest->status = PaymentRequestStatus::UPDATED;
            } else {
                $paymentRequest->status = PaymentRequestStatus::REQUESTED;
            }

            $paymentRequest->save();
            $paymentRequest->load('order.casts');
            return $this->respondWithData(PaymentRequestResource::make($paymentRequest));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }

    public function getPaymentHistory(Request $request)
    {
        $user = $this->guard()->user();

        $paymentRequests = PaymentRequest::where('cast_id', $user->id)->with('order.casts');

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
