<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ProviderType;
use App\Enums\Status;
use App\Enums\UserType;
use App\Notifications\CreateGuest;
use App\User;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Redirect;
use Socialite;

class LineController extends Controller
{
    public function login() {
        $clientId = env('LINE_KEY');
        $redirectUri = env('LINE_REDIRECT_URI');
        $scope = 'openid+profile+email';
        $state = Str::random(6);
        $url = "https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=$clientId&redirect_uri=$redirectUri&bot_prompt=normal&scope=$scope&state=$state";

//        return Socialite::driver('line')->redirect();
        return Redirect::to($url);
    }

    public function handleCallBack(Request $request) {
        if (isset($request->friendship_status_changed) || !$request->friendship_status_changed) {
            dd($request->all());
            return Socialite::driver('line')->redirect();
        }

//        $lineResponse = Socialite::driver('line')->user();
//        dd($lineResponse);
        if (!isset($request->error)) {
            $lineResponse = Socialite::driver('line')->user();

            $user = $this->findOrCreate($lineResponse);

            Auth::login($user);
        }

        return view('welcome');
    }

    protected function findOrCreate($lineResponse)
    {
        $user = User::where('line_id', $lineResponse->id)->first();

        if (!$user) {
            $data = [
                'email' => (isset($lineResponse->email)) ? $lineResponse->email : '',
                'fullname' => $lineResponse->name,
                'nickname' => ($lineResponse->nickname) ? $lineResponse->nickname : $lineResponse->name,
                'line_id' => $lineResponse->id,
                'type' => UserType::GUEST,
                'status' => Status::ACTIVE,
                'provider' => ProviderType::LINE
            ];

            $user = User::create($data);

            if ($lineResponse->avatar) {
                $user->avatars()->create([
                    'path' => $lineResponse->avatar,
                    'thumbnail' => $lineResponse->avatar,
                    'is_default' => true
                ]);
            }

            $user->notify(new CreateGuest());

            return $user;
        }

        return $user;
    }
}
