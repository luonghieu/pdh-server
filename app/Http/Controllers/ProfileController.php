<?php

namespace App\Http\Controllers;

use Auth;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use JWTAuth;
use LogService;

class ProfileController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);

            $authorization = empty($token) ?: 'Bearer ' . $token;
            $client = new Client([
                'http_errors' => false,
                'debug' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => $authorization,
                    'Content-Type' => 'application/json',
                ],
            ]);
            $apiRequest = $client->request('GET', route('auth.me'), []);

            $result = $apiRequest->getBody();
            $contents = $result->getContents();
            $contents = json_decode($contents, JSON_NUMERIC_CHECK);

            $profile = $contents['data'];

            return view('web.profile.index', compact('profile'));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
    }
}
