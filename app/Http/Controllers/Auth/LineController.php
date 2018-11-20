<?php

namespace App\Http\Controllers\Auth;

use App\Enums\DeviceType;
use App\Enums\ProviderType;
use App\Enums\Status;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Services\LogService;
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

    public function webhook(Request $request)
    {
        $header = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('LINE_BOT_CHANNEL_ACCESS_TOKEN')
        ];
        $client = new Client(['headers' => $header]);
        $response = null;

        try {
            if ($request->events[0]['type'] == 'follow') {
                $message = 'Cheersへようこそ！'
                    . PHP_EOL . 'Cheersは飲み会や接待など様々なシーンに素敵なキャストを呼べるマッチングアプリです♪'
                    . PHP_EOL . '【現在対応可能エリア】'
                    . PHP_EOL . '東京都23区'
                    . PHP_EOL . '※随時エリア拡大予定';

                $firstButton = env('LINE_LIFF_REDIRECT_PAGE');
                $secondButton = env('LINE_LIFF_REDIRECT_PAGE') . '?page=call';
                $body = [
                    'replyToken' => $request->events[0]['replyToken'],
                    'messages' => [
                        [
                            'type' => 'template',
                            'altText' => $message,
                            'text' => $message,
                            'template' => [
                                'type' => 'buttons',
                                'text' => $message,
                                'actions' => [
                                    [
                                        'type' => 'uri',
                                        'label' => 'ログイン',
                                        'uri' => "line://app/$firstButton"
                                    ],
                                    [
                                        'type' => 'uri',
                                        'label' => '今すぐキャストを呼ぶ',
                                        'uri' => "line://app/$secondButton"
                                    ]
                                ]
                            ]
                        ]
                    ],
                ];
                $body = \GuzzleHttp\json_encode($body);
                $response = $client->post(env('LINE_REPLY_URL'),
                    ['body' => $body]
                );
                return $response;
            } else {
                $message = '申し訳ございませんが、このアカウントでは個別の返信ができません。'
                    . PHP_EOL . PHP_EOL . 'サービスや予約などに関するお問い合わせは、下記からCheers運営局宛にご連絡ください。';
                $page = env('LINE_LIFF_REDIRECT_PAGE') . '?page=message';

                $body = [
                    'replyToken' => $request->events[0]['replyToken'],
                    'messages' => [
                        [
                            'type' => 'template',
                            'altText' => $message,
                            'text' => $message,
                            'template' => [
                                'type' => 'buttons',
                                'text' => $message,
                                'actions' => [
                                    [
                                        'type' => 'uri',
                                        'label' => '問い合わせる',
                                        'uri' => "line://app/$page"
                                    ],
                                ]
                            ]
                        ]
                    ],
                ];

                $body = \GuzzleHttp\json_encode($body);
                $response = $client->post(env('LINE_REPLY_URL'),
                    ['body' => $body]
                );
            }
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
        }

        return $response;
    }

    public function handleCallBack(Request $request)
    {
        try {
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

                $user = $this->findOrCreate($lineResponse)['user'];
                Auth::login($user);

                return redirect()->route('web.index');
            }

            if (!isset($request->error)) {
                if (!isset($lineResponse)) {
                    $lineResponse = Socialite::driver('line')->user();
                }

                $userData = $this->findOrCreate($lineResponse);
                $user = $userData['user'];
                Auth::login($user);
            } else {
                \Session::flash('error', trans('messages.login_line_failed'));
            }
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            \Session::flash('error', trans('messages.login_line_failed'));
        }

        if (isset($userData)) {
            $firstTime = $userData['first_time'];
        } else {
            $firstTime = false;
        }

        if ($firstTime) {
            return redirect()->route('web.index', ['first_time' => $firstTime]);
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

        $user = $user->orWhere('line_user_id', $lineResponse->id)->first();

        if (!$user) {
            $data = [
                'email' => (isset($lineResponse->email)) ? $lineResponse->email : null,
                'fullname' => $lineResponse->name,
                'nickname' => ($lineResponse->nickname) ? $lineResponse->nickname : $lineResponse->name,
                'line_user_id' => $lineResponse->id,
                'type' => UserType::GUEST,
                'status' => Status::INACTIVE,
                'provider' => ProviderType::LINE,
                'device_type' => DeviceType::WEB
            ];

            $user = User::create($data);

            if ($lineResponse->avatar) {
                $user->avatars()->create([
                    'path' => $lineResponse->avatar,
                    'thumbnail' => $lineResponse->avatar,
                    'is_default' => true,
                ]);
            }

            $user->notify(
                (new CreateGuest())->delay(now()->addSeconds(3))
            );

            return ['user' => $user, 'first_time' => true];
        }

        if (!$user->line_id) {
            $user->line_id = $lineResponse->id;
            $user->save();
        }

        $user->device_type = DeviceType::WEB;
        $user->save();

        return ['user' => $user, 'first_time' => false];
    }
}
