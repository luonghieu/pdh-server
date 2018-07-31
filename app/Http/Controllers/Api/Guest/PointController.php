<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Api\ApiController;
use App\Payment;
use App\Point;
use App\Services\LogService;
use Illuminate\Http\Request;

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
        if (!$user->cards->first()) {
            return $this->respondErrorMessage(trans('messages.card_not_exist'), 404);
        }

        \DB::beginTransaction();
        try {
            $point = new Point;
            $point->point = $request->amount;
            $point->user_id = $user->id;
            $point->status = true;
            $point->save();

            $user->point = $user->point + $request->amount;
            $user->save();

            $payment = new Payment;
            $payment->user_id = $user->id;
            $payment->amount = $request->amount;
            $payment->point_id = $point->id;
            $payment->save();

            \DB::commit();

            return $this->respondWithNoData(trans('messages.buy_point_success'));
        } catch (\Exception $e) {
            \DB::rollBack();
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }
}
