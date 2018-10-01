<?php

namespace App\Http\Controllers\Api;

use App\Enums\MessageType;
use App\Enums\ProviderType;
use App\Enums\Status;
use App\Enums\UserGender;
use App\Enums\UserType;
use App\Guest;
use App\Notifications\CreateGuest;
use App\Services\LogService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class FacebookAuthController extends ApiController
{
    public function login(Request $request)
    {
        $validator = validator($request->all(), [
            'access_token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $token = $request->access_token;

        try {
            $fbResponse = Socialite::driver('facebook')
                ->fields([
                    'email', 'name', 'first_name', 'last_name', 'gender',
                    'picture.width(400).height(400)', 'age_range', 'birthday', 'location',
                    'link'
                ])->stateless()
                ->userFromToken($token);

            $avatar = $fbResponse->avatar;
            $user = $this->findOrCreate($fbResponse->user, $avatar);

            if (!$user->status) {
                return $this->respondErrorMessage(trans('messages.login_forbidden'), 403);
            }

            $token = JWTAuth::fromUser($user);

            return $this->respondWithData($this->respondWithToken($token, $user)->getData());
        } catch (\Exception $e) {
            if ($e->getCode() == 400) {
                return $this->respondErrorMessage(trans('messages.facebook_invalid_token'), $e->getCode());
            };
            LogService::writeErrorLog($e);
            return $this->respondServerError();
        }
    }

    protected function findOrCreate($fbResponse, $avatar)
    {
        $user = User::where('facebook_id', $fbResponse['id'])->first();

        if (!$user) {
            $data = [
                'email' => (isset($fbResponse['email'])) ? $fbResponse['email'] : null,
                'fullname' => $fbResponse['name'],
                'nickname' => (isset($fbResponse['first_name'])) ? $fbResponse['first_name'] : '',
                'facebook_id' => $fbResponse['id'],
                'date_of_birth' => (isset($fbResponse['birthday'])) ? Carbon::parse($fbResponse['birthday']) : null,
                'gender' => (isset($fbResponse['gender'])) ? ($fbResponse['gender'] == 'male') ? UserGender::MALE : UserGender::FEMALE : null,
                'type' => UserType::GUEST,
                'status' => Status::ACTIVE,
                'provider' => ProviderType::FACEBOOK
            ];

            $user = User::create($data);

            if ($avatar) {
                $user->avatars()->create([
                    'path' => "$avatar&height=400&width=400",
                    'thumbnail' => "$avatar&height=400&width=400",
                    'is_default' => true
                ]);
            }

            $user->notify(new CreateGuest());

            return $user;
        }

        return $user;
    }
}
