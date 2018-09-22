<?php

namespace App\Http\Controllers;

use App\Services\LogService;
use Auth;
use JWTAuth;
use GuzzleHttp\Client;

class PointController extends Controller
{
    public function getPointsHistory()
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
            $apiRequest = $client->request('GET', route('points.points'));

            $result = $apiRequest->getBody();
            $contents = $result->getContents();
            $contents = json_decode($contents, JSON_NUMERIC_CHECK);

            $points = $contents['data'];

            return view('web.points.points_history', compact('points'));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
    }

    public function index()
    {
        $user = Auth::user();

        return view('web.point.index', compact('user'));
    }
}
