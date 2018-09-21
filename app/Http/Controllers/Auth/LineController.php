<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ProviderType;
use App\Enums\Status;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Notifications\CreateGuest;
use App\User;
use Auth;
use Socialite;

class LineController extends Controller
{
    public function login()
    {
        return Socialite::driver('line')
            ->with(['bot_prompt' => 'aggressive'])
            ->redirect();
    }

    public function handleCallBack(Request $request) {
        if (isset($request->friendship_status_changed) && $request->friendship_status_changed == 'false') {
            $redirectUri = env('LINE_REDIRECT_URI');
            $clientId = env('LINE_KEY');
            $clientSecret = env('LINE_SECRET');
            $header = [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ];
            $client = new Client([ 'headers' => $header ]);
            $response = $client->post(env('LINE_API_URI') . '/oauth2/v2.1/token',
                [
                    'form_params' => [
                        'grant_type' => 'authorization_code',
                        'code' => $request->code,
                        'redirect_uri' => $redirectUri,
                        'client_id' => $clientId,
                        'client_secret' => $clientSecret,
                    ]
                ]
            );

            $body = json_decode($response->getBody()->getContents(), true);
            $lineResponse = Socialite::driver('line')->userFromToken($body['access_token']);
        }

        if (!isset($request->error)) {
            if (!isset($lineResponse)) {
                $lineResponse = Socialite::driver('line')->user();
            }

            $user = $this->findOrCreate($lineResponse);
            Auth::login($user);
        } else {
            \Session::flash('error', trans('messages.login_line_failed'));
        }

        return redirect()->route('web.index');
    }

    protected function findOrCreate($lineResponse)
    {
        $email = $lineResponse->email;
        $user = User::query();

        if ($email) {
            $user = $user->where('email', $email);
        }

        $user = $user->orWhere('line_id', $lineResponse->id)->first();

        if (!$user) {
            $data = [
                'email' => (isset($lineResponse->email)) ? $lineResponse->email : '',
                'fullname' => $lineResponse->name,
                'nickname' => ($lineResponse->nickname) ? $lineResponse->nickname : $lineResponse->name,
                'line_id' => $lineResponse->id,
                'type' => UserType::GUEST,
                'status' => Status::ACTIVE,
                'provider' => ProviderType::LINE,
            ];

            $user = User::create($data);

            if ($lineResponse->avatar) {
                $user->avatars()->create([
                    'path' => $lineResponse->avatar,
                    'thumbnail' => $lineResponse->avatar,
                    'is_default' => true,
                ]);
            }

            $user->notify(new CreateGuest());

            return $user;
        }

        if (!$user->line_id) {
            $user->line_id = $lineResponse->id;
        }

        return $user;
    }
}
