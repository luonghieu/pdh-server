<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ProviderType;
use App\Enums\Status;
use App\Enums\UserType;
use App\Notifications\CreateGuest;
use App\User;
use App\Http\Controllers\Controller;
use Auth;
use Socialite;

class LineController extends Controller
{
    public function login()
    {
        return Socialite::driver('line')->redirect();
    }

    public function handleCallBack()
    {
        $lineResponse = Socialite::driver('line')->user();

        $user = $this->findOrCreate($lineResponse);

        Auth::login($user);
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
