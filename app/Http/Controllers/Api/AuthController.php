<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Resources\CastResource;
use App\Http\Resources\GuestResource;
use App\Rules\CheckHeight;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class AuthController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login()
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
            'type' => 'required|in:1,2',
        ];

        $validator = validator(request()->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        if (6 > strlen(request()->password)) {
            return $this->respondErrorMessage(trans('messages.password_min_invalid'), 400);
        }

        $credentials = request()->only('email', 'password');
        if (($token = $this->guard()->attempt($credentials)) && request('type') == $this->guard()->user()->type) {
            return $this->respondWithData($this->respondWithToken($token, $this->guard()->user())->getData());
        }

        return $this->respondErrorMessage(trans('messages.login_error'), 401);
    }

    public function refresh()
    {
        return $this->respondWithData($this->respondWithToken($this->guard()->refresh(), $this->guard()->user())->getData());
    }

    public function logout()
    {
        $this->guard()->logout();

        return $this->respondWithNoData(trans('messages.logout_success'));
    }

    public function me()
    {
        $user = $this->guard()->user();

        if (UserType::CAST == $user->type) {
            $user = $user->load('bankAccount');

            return $this->respondWithData(CastResource::make($user));
        }

        $user = $user->load('card');

        return $this->respondWithData(GuestResource::make($user));
    }

    public function update(Request $request)
    {
        $user = $this->guard()->user();
        $rules = [
            'nickname' => 'max:20',
            'date_of_birth' => 'date|before:today',
            'gender' => 'in:0,1,2',
            'intro' => 'max:30',
            'description' => 'max:1000',
            'phone' => 'max:13',
            'living_id' => 'numeric|exists:prefectures,id',
            'cost' => 'numeric',
            'salary_id' => 'numeric|exists:salaries,id',
            'height' => ['numeric', new CheckHeight],
            'body_type_id' => 'numeric|exists:body_types,id',
            'hometown_id' => 'numeric|exists:prefectures,id',
            'job_id' => 'numeric|exists:jobs,id',
            'drink_volume_type' => 'numeric|between:0,3',
            'smoking_type' => 'numeric|between:0,3',
            'siblings_type' => 'numeric|between:0,3',
            'cohabitant_type' => 'numeric|between:0,4',
            'front_id_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'back_id_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'line_qr' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'fullname_kana' => 'string|regex:/^[ぁ-ん ]/u',
        ];
        $validator = validator(request()->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        if ($request->post_code) {
            $subject = $request->post_code;
            $count = strlen($subject);

            if ($count < 7) {
                return $this->respondErrorMessage(trans('messages.postcode_invalid'), 400);
            }

//            $pattern = '/^(1[0-9]|20)\d{1}[-]?\d{4}$/';
            //
            //            if (preg_match($pattern, $subject) == false) {
            //                return $this->respondErrorMessage(trans('messages.postcode_not_support'), 422);
            //            }
        }

        $input = request()->only([
            'nickname',
            'date_of_birth',
            'gender',
            'description',
            'intro',
            'phone',
            'living_id',
            'cost',
            'salary_id',
            'height',
            'body_type_id',
            'hometown_id',
            'job_id',
            'drink_volume_type',
            'smoking_type',
            'siblings_type',
            'cohabitant_type',
            'line_id',
            'post_code',
            'address',
            'fullname_kana',
            'fullname',
        ]);

        try {
            $frontImage = $request->file('front_id_image');
            if ($frontImage) {
                $frontImageName = Uuid::generate()->string . '.' . strtolower($frontImage->getClientOriginalExtension());
                Storage::put($frontImageName, file_get_contents($frontImage), 'public');

                $input['front_id_image'] = $frontImageName;
            }

            $backImage = $request->file('back_id_image');
            if ($backImage) {
                $backImageName = Uuid::generate()->string . '.' . strtolower($backImage->getClientOriginalExtension());
                Storage::put($backImageName, file_get_contents($backImage), 'public');

                $input['back_id_image'] = $backImageName;
            }

            $lineImage = $request->file('line_qr');
            if ($lineImage) {
                $lineImageName = Uuid::generate()->string . '.' . strtolower($lineImage->getClientOriginalExtension());
                Storage::put($lineImageName, file_get_contents($lineImage), 'public');

                $input['line_qr'] = $lineImageName;
            }

            if (isset($input['intro']) && md5($input['intro']) != md5($user->intro)) {
                $input['intro_updated_at'] = now();
            }

            $user->update($input);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return $this->respondServerError();
        }

        if (UserType::CAST == $user->type) {
            return $this->respondWithData(CastResource::make($user));
        }

        return $this->respondWithData(GuestResource::make($user));
    }
}
