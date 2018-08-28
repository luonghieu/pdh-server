<?php

namespace App\Http\Controllers\Api\Guest;

use App\Cast;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use App\Enums\CastOrderStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CastResource;
use App\Http\Controllers\Api\ApiController;

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
                ->on('co.updated_at', '=', DB::raw('(select max(updated_at) from cast_order where user_id = co.user_id)'))
                ->where('co.status', '=', CastOrderStatus::DONE);
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
            ->select('users.*')
            ->paginate($request->per_page)
            ->appends($request->query());

        $casts = $casts->map(function ($item) {
            $item->latest_order_flag = true;
            return $item;
        });

        return $this->respondWithData(CastResource::collection($casts));
    }
}
