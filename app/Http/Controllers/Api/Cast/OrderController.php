<?php

namespace App\Http\Controllers\Api\Cast;

use App\Criteria\Order\FilterByScopeCriteria;
use App\Criteria\Order\FilterByStatusCriteria;
use App\Criteria\Order\OnlyCastCriteria;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\OrderResource;
use App\Order;
use App\Repositories\OrderRepository;

class OrderController extends ApiController
{
    protected $repository;

    public function __construct()
    {
        $this->repository = app(OrderRepository::class);
    }

    public function index()
    {
        $this->repository->pushCriteria(OnlyCastCriteria::class);
        $this->repository->pushCriteria(FilterByStatusCriteria::class);

        if (!empty(request()->scope)) {
            $this->repository->pushCriteria(FilterByScopeCriteria::class);
        }

        $orders = $this->repository->with('user')->paginate();

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

        $nomineesIds = $order->nominees()->wherePivot('canceled_at', null)->pluck('cast_order.user_id')->toArray();

        $user = $this->guard()->user();

        if (!in_array($user->id, $nomineesIds)) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        if (!$order->deny($user->id)) {
            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.denied_order'));
    }

    public function apply($id)
    {
        $order = Order::with('casts')->where('type', OrderType::CALL)->find($id);

        if (!$order) {
            return $this->respondErrorMessage(trans('messages.order_not_found'), 404);
        }

        if ($order->casts->count() == $order->total_cast) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        $user = $this->guard()->user();

        if (!$order->apply($user->id)) {
            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.confirm_order'));
    }
}
