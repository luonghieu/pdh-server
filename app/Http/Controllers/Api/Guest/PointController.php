<?php

namespace App\Http\Controllers\Api\Guest;

use App\Point;
use App\Payment;
use App\Enums\PaymentStatus;
use App\Enums\ResignStatus;
use App\Services\LogService;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;

class PointController extends ApiController
{
    public function buy(Request $request)
    {
        $user = $this->guard()->user();
        if ($user->resign_status == ResignStatus::PENDING) {
            return $this->respondErrorMessage(trans('messages.buy_point_resign_status_pending'), 403);
        }

        $rules = [
            'amount' => 'required|numeric',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }


        $now = Carbon::now();

        if (!$user->is_card_registered) {
            return $this->respondErrorMessage(trans('messages.card_not_exist'), 404);
        }

        try {
            \DB::beginTransaction();
            $point = $user->buyPoint($request->amount);
            \DB::commit();
            return $this->respondWithNoData(trans('messages.buy_point_success'));
        } catch (\Exception $e) {
            \DB::rollBack();
            LogService::writeErrorLog('---------- Buy Point ------------');
            LogService::writeErrorLog($e);
            LogService::writeErrorLog('---------------------------------');
            return $this->respondServerError();
        }
    }
}
