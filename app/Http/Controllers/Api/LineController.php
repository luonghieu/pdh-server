<?php

namespace App\Http\Controllers\Api;

use App\Services\LogService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LineController extends Controller
{
    public function webhook(Request $request)
    {
        if ($request->events[0]['type'] == 'follow') {
            try {
                $header = [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . env('LINE_BOT_CHANNEL_ACCESS_TOKEN')
                ];
                $client = new Client(['headers' => $header]);

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
            } catch (\Exception $e) {
                LogService::writeErrorLog($e);
            }
        }

        return;
    }
}
