<?php

namespace App\Http\Controllers\Api\Guest;

use App\Point;
use App\Payment;
use App\Enums\PaymentStatus;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;

class PointController extends ApiController
{
    public function buy(Request $request)
    {
        $rules = [
            'amount' => 'required|numeric',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();
        $now = Carbon::now();

        if (!$user->tc_send_id) {
            return $this->respondErrorMessage(trans('messages.card_not_exist'), 404);
        }

        $point = $user->buyPoint($request->amount);

        if (!$point) {
            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.buy_point_success'));
    }
}
