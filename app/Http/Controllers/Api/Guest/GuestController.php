<?php

namespace App\Http\Controllers\Api\Guest;

use App\Cast;
use App\Enums\OrderStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\CastResource;
use Illuminate\Http\Request;

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

        $casts = Cast::with([
            'orders' => function ($query) use ($user) {
                $query
                    ->where('orders.user_id', $user->id)
                    ->where('orders.status', OrderStatus::DONE);
            },
        ])->whereHas('orders');

        if ($request->nickname) {
            $nickname = $request->nickname;
            $casts = $casts->where('nickname', $nickname);
        }

        $casts = $casts->paginate($request->per_page)->appends($request->query());

        return $this->respondWithData(CastResource::collection($casts));
    }
}
