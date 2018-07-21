<?php

namespace App\Http\Controllers\Api\Cast;

use App\Repositories\OrderRepository;
use App\Http\Resources\OrderResource;
use App\Http\Controllers\Api\ApiController;
use App\Criteria\Order\FilterByScopeCriteria;
use App\Criteria\Order\FilterByStatusCriteria;
use App\Criteria\Order\OnlyCastCriteria;

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

        if (! empty(request()->scope)) {
            $this->repository->pushCriteria(FilterByScopeCriteria::class);
        }

        $orders = $this->repository->with('user')->paginate();

        return $this->respondWithData(OrderResource::collection($orders));
    }
}
