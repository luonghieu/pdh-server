<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use Socialite;
use App\Enums\Status;
use App\Enums\UserType;
use App\Enums\ProviderType;
use App\Notifications\CreateGuest;
use App\Http\Controllers\Controller;

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

        return redirect()->route('web.index');
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
