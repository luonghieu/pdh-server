<?php

namespace App\Http\Controllers\Api\Guest;

use App\Point;
use App\Payment;
use App\Enums\PaymentStatus;
use App\Services\LogService;
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
        if (!$user->card) {
            return $this->respondErrorMessage(trans('messages.card_not_exist'), 404);
        }

        try {
            \DB::beginTransaction();

            $point = new Point;
            $point->point = $request->amount;
            $point->user_id = $user->id;
            $point->status = false;
            $point->save();

            $payment = new Payment;
            $payment->user_id = $user->id;
            $payment->amount = ($request->amount) * 1.1;
            $payment->point_id = $point->id;
            $payment->card_id = $user->card->id;
            $payment->status = PaymentStatus::OPEN;
            $payment->save();

            // charge money
            $charged = $payment->charge();

            if ($charged) {
                $point->status = true;
                $point->balance = $point->point + $user->point;
                $point->save();

                $user->point = $user->point + $request->amount;
                $user->save();
            }

            \DB::commit();

            return $this->respondWithNoData(trans('messages.buy_point_success'));
        } catch (\Exception $e) {
            \DB::rollBack();
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }
}
