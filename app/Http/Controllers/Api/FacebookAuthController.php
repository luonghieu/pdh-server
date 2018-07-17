<?php

namespace App\Http\Controllers\Api;

use App\Enums\MessageType;
use App\Enums\UserGender;
use App\Enums\UserType;
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

            $user = $this->findOrCreate($fbResponse->user);

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

    protected function findOrCreate($fbResponse)
    {
        $user = User::where('facebook_id', $fbResponse['id'])->first();

        if (!$user) {
            $data = [
                'email' => (isset($fbResponse['email'])) ? $fbResponse['email'] : '',
                'fullname' => $fbResponse['name'],
                'nickname' => (isset($fbResponse['first_name'])) ? $fbResponse['first_name'] : '',
                'facebook_id' => $fbResponse['id'],
                'date_of_birth' => (isset($fbResponse['birthday'])) ? Carbon::parse($fbResponse['birthday']) : null,
                'gender' => (isset($fbResponse['gender'])) ? ($fbResponse['gender'] == 'male') ? UserGender::MALE : UserGender::FEMALE : null,
                'type' => UserType::GUEST,
            ];
            $user = User::create($data);

            $user->avatars()->create([
                'path' => $fbResponse['picture']['data']['url'],
                'thumbnail' => $fbResponse['picture']['data']['url'],
                'is_default' => true
            ]);

            $user = User::find($user->id); // Return user with full attributes.

            $message = 'ようこそCheersへ！'
                .'\nCheersはプライベートでの飲み会や接待など様々なシーンにキャストを呼べるマッチングアプリです。'
                .'\nクオリティの高いキャストと今すぐ出会えるのはCheersだけ！'
                .'\n呼びたいときに、呼びたい人数・場所を入力するだけ。'
                .'\n最短20分でキャストがゲストの元に駆けつけます♪'
                .'\n「キャスト一覧」からお気に入りのキャストを見つけてアピールすることも可能です！'
                .'\nまずはHomeの「今すぐキャストを呼ぶ」からキャストを呼んで素敵な時間をお過ごし下さい♪'
                .'\nご不明点はお気軽にお問い合わせください。';

            $room = $user->rooms()->create();
            $room->users()->attach(1);

            $room->messages()->create([
                'user_id' => 1,
                'type' => MessageType::SYSTEM,
                'message' => $message
            ]);
            return $user;
        }

        return $user;
    }
}
