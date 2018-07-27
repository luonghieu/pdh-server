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

        $orders = Order::with('user', 'tags');

        if (isset($request->scope)) {
            if (OrderScope::OPEN_TODAY == $request->scope) {
                $today = Carbon::today();
                $orders->whereDate('date', $today);
            } else {
                $tomorow = Carbon::tomorrow();
                $orders->whereDate('date', '>=', $tomorow);
            }

            $orders->where(function ($query) use ($user) {
                $query->whereDoesntHave('nominees', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->whereDoesntHave('casts', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            })
                ->where('type', OrderType::CALL)
                ->where('status', OrderStatus::OPEN)
                ->where('class_id', '<=', $user->class_id)
                ->orderBy('date')
                ->orderBy('start_time');
        } elseif (isset($request->status)) {
            $orders->where(function ($query) use ($user) {
                $query->whereHas('nominees', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            });

            $orders->where('status', $request->status);
        } else {
            $orders->where(function ($query) use ($user) {
                $query->whereHas('nominees', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->whereNotNull('cast_order.accepted_at');
                });
            })
                ->orderBy('date')
                ->orderBy('start_time');
        }

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

        $user = $this->guard()->user();

        $nomineeExists = $order->nominees()->where('user_id', $user->id)->whereNull('canceled_at')->first();

        if (!$nomineeExists) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        if (!$order->deny($user->id)) {
            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.denied_order'));
    }

    public function apply($id)
    {
        $order = Order::where('status', OrderStatus::OPEN)->find($id);

        if (!$order) {
            return $this->respondErrorMessage(trans('messages.order_not_found'), 404);
        }

        $user = $this->guard()->user();
        $nomineeExists = $order->nominees()->where('user_id', $user->id)->whereNull('accepted_at')->first();

        if (!$nomineeExists) {
            if ((OrderType::CALL != $order->type) || $order->casts->count() == $order->total_cast
                || $order->candidates->contains($user->id)) {
                return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
            }

            if (!$order->apply($user->id)) {
                return $this->respondServerError();
            }

            return $this->respondWithNoData(trans('messages.accepted_order'));
        }

        if (!$order->accept($user->id)) {
            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.accepted_order'));
    }

    public function start($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return $this->respondErrorMessage(trans('messages.order_not_found'), 404);
        }

        $user = $this->guard()->user();
        $castExists = $order->casts()->where('user_id', $user->id)->whereNull('started_at')->first();

        $validStatus = [
            OrderStatus::ACTIVE,
            OrderStatus::PROCESSING,
        ];

        if (!$castExists || !in_array($order->status, $validStatus)) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        if (!$order->start($user->id)) {
            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.start_order'));
    }

    public function stop($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return $this->respondErrorMessage(trans('messages.order_not_found'), 404);
        }

        $user = $this->guard()->user();
        $castExists = $order->casts()->where('user_id', $user->id)->whereNull('stopped_at')->first();

        $validStatus = [
            OrderStatus::PROCESSING,
            OrderStatus::DONE,
        ];

        if (!$castExists || !in_array($order->status, $validStatus)) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        if (!$order->stop($user->id)) {
            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.stop_order'));
    }
}
