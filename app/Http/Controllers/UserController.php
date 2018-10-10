<?php

namespace App\Http\Controllers;

use App\Services\LogService;
use Auth;
use GuzzleHttp\Client;
use JWTAuth;

class UserController extends Controller
{
    public function show($id)
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
            $apiRequest = $client->get('/api/v1/users/' . $id);

            $result = $apiRequest->getBody();
            $contents = $result->getContents();
            $contents = json_decode($contents, JSON_NUMERIC_CHECK);
            $cast = $contents['data'];

            return view('web.users.show', compact('cast'));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
    }
}