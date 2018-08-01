<?php

namespace App\Http\Controllers\Api\Cast;

use App\Enums\CastOrderStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentRequestStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\PaymentRequestResource;
use App\Order;
use App\PaymentRequest;
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
            ['status', PaymentRequestStatus::OPEN],
        ])->with('cast', 'guest')->get();

        return $this->respondWithData(PaymentRequestResource::collection($paymentRequest));
    }
}
