<?php

namespace App\Http\Controllers\Auth;

use App\Services\LogService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LineNotifyController extends Controller
{
    public function webhook(Request $request)
    {
        LogService::writeErrorLog($request->events);
        if ($request->events[0]['type'] == 'join') {
            putPermanentEnv('LINE_GROUP_ID', $request->events[0]['source']['groupId']);

            $header = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('LINE_BOT_NOTIFY_CHANNEL_ACCESS_TOKEN')
            ];
            $client = new Client(['headers' => $header]);

            $body = [
                'replyToken' => $request->events[0]['replyToken'],
                'messages' => [
                    [
                        'type' => 'text',
                        'text' => 'Bot Joined.',
                    ]
                ]
            ];

            $body = \GuzzleHttp\json_encode($body);
            $client->post(env('LINE_REPLY_URL'),
                ['body' => $body]
            );
        }
    }
}
