<?php

namespace App\Http\Controllers\Api\Guest;

use App\Enums\OrderStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\OrderResource;
use App\Order;
use Illuminate\Http\Request;

class OrderController extends ApiController
{
    public function index(Request $request)
    {
        $user = $this->guard()->user();
        $orders = Order::whereIn('status', [OrderStatus::OPEN, OrderStatus::ACTIVE, OrderStatus::PROCESSING])
            ->where('user_id', $user->id)
            ->with(['user', 'casts', 'nominees', 'tags'])
            ->latest()
            ->paginate($request->per_page);

        return $this->respondWithData(OrderResource::collection($orders));
    }

    public function cancel($id)
    {
        $user = $this->guard()->user();

        $order = $user->orders()->find($id);

        if (!$order) {
            return $this->respondErrorMessage(trans('messages.order_not_found'), 404);
        }

        if (!in_array($order->status, [OrderStatus::OPEN, OrderStatus::ACTIVE])) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        if (!$order->cancel()) {
            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.cancel_order_success'));
    }
}
