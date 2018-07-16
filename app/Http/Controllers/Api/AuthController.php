<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Resources\CastResource;
use App\Http\Resources\GuestResource;
use App\Rules\CheckHeight;
use App\User;
use Illuminate\Http\Request;

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
        return $this->respondWithData($this->respondWithToken($this->guard()->refresh())->getData());
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
            return $this->respondWithData(CastResource::make($user));
        }

        return $this->respondWithData(GuestResource::make($user));
    }

    public function update(Request $request)
    {
        $user = $this->guard()->user();
        $rules = [
            'nickname' => 'max:20',
            'date_of_birth' => 'date|before:today',
            'gender' => 'in:1,2',
            'intro' => 'max:30',
            'description' => 'max:1000',
            'phone' => 'max:13',
            'prefecture_id' => 'numeric|exists:prefectures,id',
            'cost' => 'numeric',
            'salary_id' => 'numeric|exists:salaries,id',
            'height' => ['numeric', new CheckHeight],
            'body_type_id' => 'numeric|exists:body_types,id',
            'hometown_id' => 'numeric|exists:prefectures,id',
            'job_id' => 'numeric|exists:jobs,id',
            'drink_volume_type' => 'numeric|between:1,3',
            'smoking_type' => 'numeric|between:1,3',
            'cohabitant_type' => 'numeric|between:1,4',
            'line_id' => 'string',
        ];

        $validator = validator(request()->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $input = request()->only([
            'nickname',
            'date_of_birth',
            'gender',
            'description',
            'intro',
            'phone',
            'prefecture_id',
            'cost',
            'salary_id',
            'height',
            'body_type_id',
            'hometown_id',
            'job_id',
            'drink_volume_type',
            'smoking_type',
            'cohabitant_type',
            'line_id',
        ]);

        try {
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
