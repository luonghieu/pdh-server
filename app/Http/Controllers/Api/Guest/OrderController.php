<?php

namespace App\Http\Controllers\Api\Guest;

use App\Repositories\OrderRepository;
use App\Http\Resources\OrderResource;
use App\Http\Controllers\Api\ApiController;
use App\Criteria\Order\FilterByStatusCriteria;
use App\Criteria\Order\OnlyGuestCriteria;

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
}
