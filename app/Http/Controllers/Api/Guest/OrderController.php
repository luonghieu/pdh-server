<?php

namespace App\Http\Controllers\Api\Guest;

use App\Criteria\Order\FilterByStatusCriteria;
use App\Criteria\Order\OnlyGuestCriteria;
use App\Enums\OrderStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\OrderResource;
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
        $this->repository->pushCriteria(OnlyGuestCriteria::class);
        $this->repository->pushCriteria(FilterByStatusCriteria::class);

        $orders = $this->repository->with('user')->paginate();

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

        $order->cancel();

        return $this->respondWithNoData(trans('messages.cancel_order_success'));
    }
}
