<?php

namespace App\Services;

use App\Exceptions\LineConfigNotFoundException;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class RocketChat {

    private $apiUrl;
    private $userId;
    private $token;
    private $client;
    private $headers;

    public function __construct()
    {
        $this->userId = env('ROCKET_CHAT_USER_ID');
        $this->token = env('ROCKET_CHAT_USER_TOKEN');
        $this->apiUrl = env('ROCKET_CHAT_API_URL');

        if (!$this->userId && !$this->token && !$this->apiUrl) {
            throw new LineConfigNotFoundException('ENV configurations not found');
        }

        $this->headers = [
            'Content-Type' => 'application/json',
            'X-Auth-Token' => $this->token,
            'X-User-Id' => $this->userId,
        ];
        $this->client = new Client([ 'headers' => $this->headers ]);
    }

    public function sendMessage($attachments = [], $channel = null)
    {
        try {
            $body = [
                'channel' => ($channel) ? $channel : env('ROCKET_CHAT_DEFAULT_CHANNEL'),
                'attachments' => [$attachments]
            ];

            $body = \GuzzleHttp\json_encode($body);
            $response = $this->client->post($this->apiUrl . '/chat.postMessage', ['body' => $body]);

            return $response;
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
        }

        return;
    }
}