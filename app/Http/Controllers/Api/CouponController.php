<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Coupon;
use Illuminate\Pagination\LengthAwarePaginator;

class CouponController extends ApiController
{
    public function getCoupons(Request $request)
    {
        $rules = [
            'duration' => 'required|numeric',
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


        if ($params['duration']) {
            $coupons = $coupons->where([
                ['is_filter_order_duration', '=', true],
                ['filter_order_duration', '>=', $params['duration']],
            ]);
        }
        $coupons = $coupons->get();
        $now = now();
        $collection = $coupons->reject(function ($item) use ($user, $now) {
            $createdAtOfUser = Carbon::parse($user->created_at);

            $bool = false;
            if ($now->diffInDays($createdAtOfUser) <= $item->filter_after_created_date) {
                $bool = true;
            }

            return $bool;
        });

        return $this->respondWithData($collection);

    }
}
