<?php

namespace App\Http\Controllers\Api\Bot;

use App\Services\LogService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class LineController extends Controller
{
    public function webhook(Request $request) {
        if ($request->events[0]['type'] != 'follow') {
            try {
                $header = [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . env('LINE_BOT_CHANNEL_ACCESS_TOKEN')
                ];
                $client = new Client([ 'headers' => $header ]);
                $body = [
                    'replyToken' => $request->events[0]['replyToken'],
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => 'Hello, Boi'
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

        return null;
    }

    public function sendMessage() {
        try {
            $header = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('LINE_BOT_CHANNEL_ACCESS_TOKEN')
            ];

            $body = [
                'to' => 'Ub1ed07600492fa5895fca997f1b830d0',
                'messages' => [
                    [
                        'type' => 'template',
                        'altText' => 'Button template',
                        'text' => 'bobobobo',
                        'template' => [
                            'type' => 'buttons',
                            'thumbnailImageUrl' => 'https://i.ytimg.com/vi/d_T5P-zIIAs/maxresdefault.jpg',
                            'imageAspectRatio' => 'rectangle',
                            'imageSize' => 'cover',
                            'imageBackgroundColor' => '#FFFFFF',
                            'title' => 'Title (Can remove)',
                            'text' => 'Text here',
                            'defaultAction' => [
                                'type' => 'uri',
                                'label' => 'View detail',
                                'uri' => 'https://news.zing.vn/'
                            ],
                            'actions' => [
                                [
                                    'type' => 'uri',
                                    'label' => 'View detail',
                                    'uri' => 'https://news.zing.vn/'
                                ]
                            ]
                        ]
                    ],
                ],
            ];

            // Button Exam
//            [
//                'type' => 'template',
//                'altText' => 'Button template',
//                'text' => 'bobobobo',
//                'template' => [
//                    'type' => 'buttons',
//                    'thumbnailImageUrl' => 'https://i.ytimg.com/vi/d_T5P-zIIAs/maxresdefault.jpg',
//                    'imageAspectRatio' => 'rectangle',
//                    'imageSize' => 'cover',
//                    'imageBackgroundColor' => '#FFFFFF',
//                    'title' => 'Menu',
//                    'text' => 'Please Select',
//                    'defaultAction' => [
//                        'type' => 'uri',
//                        'label' => 'View detail',
//                        'uri' => 'https://news.zing.vn/'
//                    ],
//                    'actions' => [
//                        [
//                            'type' => 'postback',
//                            'label' => 'Buy',
//                            'data' => 'action=buy&itemid=123'
//                        ],
//                        [
//                            'type' => 'postback',
//                            'label' => 'Add to cart',
//                            'data' => 'action=add&itemid=123'
//                        ],
//                        [
//                            'type' => 'uri',
//                            'label' => 'View detail',
//                            'uri' => 'https://news.zing.vn/'
//                        ]
//                    ]
//                ]
//            ]

            $body = \GuzzleHttp\json_encode($body);
            $client = new Client([ 'headers' => $header ]);
            $response = $client->post(env('LINE_PUSH_URL'),
                ['body' => $body]
            );
            return $response;
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
        }

        return null;
    }
}
