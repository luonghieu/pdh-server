<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\CouponResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Coupon;

class CouponController extends ApiController
{
    public function getCoupons(Request $request)
    {
        $rules = [
            'duration' => 'numeric',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $params = $request->only([
            'duration',
        ]);

        $user = $this->guard()->user();

        $coupons = Coupon::whereDoesntHave('users', function($q) use ($user) {
            $q->where('user_id', '=', $user->id);
        });

        if (isset($params['duration'])) {
            $coupons = $coupons->where([
                ['is_filter_order_duration', '=', true],
                ['filter_order_duration', '>=', $params['duration']],
            ])->orWhere('is_filter_order_duration', false);
        } else {
            $coupons = $coupons->where('is_filter_order_duration', false);
        }

        $coupons = $coupons->get();
        $now = now();
        $collection = $coupons->reject(function ($item) use ($user, $now) {
            $createdAtOfUser = Carbon::parse($user->created_at);

            $bool = false;
            if ($item->is_filter_after_created_date && $item->filter_after_created_date) {
                if ($now->diffInDays($createdAtOfUser) > $item->filter_after_created_date) {
                    $bool = true;
                }
            }

            return $bool;
        })->values();

        return $this->respondWithData(CouponResource::collection($collection));

    }
}
