<?php

namespace App\Http\Controllers;

use App\Services\LogService;
use Auth;
use GuzzleHttp\Client;
use JWTAuth;

class PaymentController extends Controller
{
    public function history()
    {
        try {
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);

            $authorization = empty($token) ?: 'Bearer ' . $token;
            $client = new Client([
                'base_uri' => config('common.api_url'),
                'http_errors' => false,
                'debug' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => $authorization,
                    'Content-Type' => 'application/json',
                ],
            ]);
            $payments = $client->request('GET', '/api/v1/cast/payments');

            $payments = json_decode(($payments->getBody())->getContents(), JSON_NUMERIC_CHECK);
            $payments = $payments['data'];

            return view('web.payments.index', compact('payments'));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
    }

    public function loadMore()
    {
        try {
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);

            $authorization = empty($token) ?: 'Bearer ' . $token;
            $client = new Client([
                'base_uri' => config('common.api_url'),
                'http_errors' => false,
                'debug' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => $authorization,
                    'Content-Type' => 'application/json',
                ],
            ]);
            $payments = $client->request('GET', request()->next_page);

            $payments = json_decode(($payments->getBody())->getContents(), JSON_NUMERIC_CHECK);
            $payments = $payments['data'];

            return [
                'next_page' => $payments['next_page_url'],
                'view' => view('web.payments.list_payments', compact('payments'))->render(),
            ];
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
    }
}
