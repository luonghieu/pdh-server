<?php

namespace App\Http\Controllers;

use App\Services\LogService;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class TimeLineController extends Controller
{
    public function index(Request $request)
    {
        $userId = null;
        if ($request->user_id) {
            $userId = $request->user_id;

            if (!User::find($userId)) {
                return redirect()->route('web.index');
            }
        }

        return view('web.timelines.index', compact('userId'));
    }

    public function show(Request $request)
    {
        return view('web.timelines.show');
    }

    public function create(Request $request)
    {
        return view('web.timelines.create');
    }

    public function loadMoreListTimelines(Request $request)
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

            $timelines = $client->request('GET', request()->next_page);

            $timelines = json_decode(($timelines->getBody())->getContents(), JSON_NUMERIC_CHECK);
            $timelines = $timelines['data'];

            return [
                'next_page' => $timelines['next_page_url'],
                'view' => view('web.timelines.load_more_timelines', compact('timelines'))->render(),
            ];
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
    }
}
