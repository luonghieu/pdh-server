<?php

namespace App\Http\Controllers\Api\Cast;

use App\Enums\CastOrderStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\PaymentResource;
use App\Order;
use Illuminate\Http\Request;

class PaymentRequestController extends ApiController
{
    public function payment(Request $request, $id)
    {
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
            ])->with('orders')
            ->whereNotNull('stopped_at')->get();
        dd($cast);
        if (!$cast) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        return $this->respondWithData(PaymentResource::collection($cast));
    }
}
