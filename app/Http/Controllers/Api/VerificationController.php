<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Enums\Status;
use Illuminate\Http\Request;
use App\Notifications\SendVerificationCode;
use App\Notifications\ResendVerificationCode;

class VerificationController extends ApiController
{
    public function code(Request $request)
    {
        $rules = [
            'phone' => 'phone:' . config('common.phone_number_rule'),
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $phone = $request->phone;

        $user = $this->guard()->user();

        $verification = $user->generateVerifyCode($phone);

        $user->notify(
            (new SendVerificationCode($verification->id))->delay(now()->addSeconds(2))
        );

        return $this->respondWithNoData(trans('messages.verification_code_sent'));
    }

    public function resend(Request $request)
    {
        $user = $this->guard()->user();
        $verification = $user->verification;

        if (!$verification) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        $newVerification = $user->generateVerifyCode($verification->phone, true);

        $admin = User::find(1);
        $admin->notify(
            (new ResendVerificationCode($newVerification->id))->delay(now()->addSeconds(3))
        );

        return $this->respondWithNoData(trans('messages.verification_code_sent'));
    }

    public function verify(Request $request)
    {
        $rules = [
            'code' => 'digits:4',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $code = $request->code;
        $user = $this->guard()->user();
        $isVerified = $user->is_verified;
        $verification = $user->verification;

        if (!$verification) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        if ($code != $verification->code) {
            return $this->respondErrorMessage(trans('messages.verification_code_is_wrong'), 400);
        }

        $verification->status = Status::ACTIVE;
        $verification->save();

        $user->phone = $verification->phone;
        $user->is_verified = true;
        $user->status = Status::ACTIVE;
        $user->save();

        if (!$isVerified) {
            return $this->respondWithNoData(trans('messages.user_verify_success'));
        }

        return $this->respondWithNoData(trans('messages.phone_update_success'));
    }
}
