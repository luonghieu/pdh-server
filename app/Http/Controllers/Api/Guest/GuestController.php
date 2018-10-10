<?php

namespace App\Http\Controllers\Api\Guest;

use App\Cast;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use App\Enums\CastOrderStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CastResource;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Pagination\LengthAwarePaginator;

class GuestController extends ApiController
{
    public function castHistories(Request $request)
    {
        $rules = [
            'per_page' => 'numeric|min:1',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();

        $casts = Cast::join('cast_order as co', function ($query) {
            $query->on('co.user_id', '=', 'users.id')
                ->where('co.status', '=', CastOrderStatus::DONE);
        })->join('orders as o', function ($query) {
            $query->on('o.id', '=', 'co.order_id')
                ->where('o.status', OrderStatus::DONE);
        })->whereHas('orders', function ($query) use ($user) {
            $query->where('orders.user_id', $user->id)
                ->where('orders.status', OrderStatus::DONE);
        });

        if ($request->nickname) {
            $nickname = $request->nickname;
            $casts = $casts->where('nickname', 'like', "%$nickname%");
        }

        $casts = $casts->groupBy('users.id')
            ->orderByDesc('co.updated_at')
            ->orderByDesc('o.updated_at')
            ->select('users.*')
            ->get();

        $casts = $casts->each->setAppends(['latest_order'])
            ->sortByDesc('latest_order.pivot.updated_at')
            ->values();

        $casts = $this->paginate($casts, $request->per_page ?: 15, $request->page);

        $casts = $casts->map(function ($item) {
            $item->latest_order_flag = true;

            return $item;
        });

        return $this->respondWithData(CastResource::collection($casts));
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ? : (Paginator::resolveCurrentPage() ? : 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}