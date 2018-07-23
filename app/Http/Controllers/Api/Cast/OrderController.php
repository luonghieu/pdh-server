<?php

namespace App\Http\Controllers\Api\Cast;

use App\Enums\OrderScope;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\OrderResource;
use App\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends ApiController
{
    public function index(Request $request)
    {
        $rules = [
            'scope' => 'numeric|in:1,2',
            'status' => 'numeric|min:1|max:7',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();

        $orders = Order::with('user');

        $status = [OrderStatus::OPEN, OrderStatus::ACTIVE];
        if ($request->status) {
            $status = [$request->status];
        }

        if (isset($request->scope)) {
            if ($request->scope == OrderScope::OPEN_TODAY) {
                $today = Carbon::today();
                $orders->whereDate('date', $today);
            } else {
                $tomorow = Carbon::tomorrow();
                $orders->whereDate('date', '>=', $tomorow);
            }

            $orders->where(function($query) use ($user) {
                $query->whereHas('nominees', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->orWhere('type', OrderType::CALL);
            });
        } else {
            $orders->whereHas('nominees', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        $orders->whereIn('status', $status);
        $orders = $orders->paginate($request->per_page)->appends($request->query());

        return $this->respondWithData(OrderResource::collection($orders));
    }

    public function deny($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->respondErrorMessage(trans('messages.order_not_found'), 404);
        }

        if (OrderStatus::OPEN != $order->status) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        $nomineesIds = $order->nominees()->pluck('cast_order.user_id')->toArray();

        $user = $this->guard()->user();

        if (!in_array($user->id, $nomineesIds)) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        if (!$order->deny($user->id)) {
            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.denied_order'));
    }
}
