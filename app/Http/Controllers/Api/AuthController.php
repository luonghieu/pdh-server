<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Resources\CastResource;
use App\Http\Resources\GuestResource;
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
            return $this->respondWithData($this->respondWithToken($token)->getData());
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
}
