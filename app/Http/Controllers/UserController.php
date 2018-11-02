<?php

namespace App\Http\Controllers;

use App\Services\LogService;
use Auth;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use JWTAuth;

class UserController extends Controller
{
    public function getApi($url, $query = [])
    {
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
        $apiRequest = $client->request('GET', $url, [
            'query' => $query,
        ]);

        $result = $apiRequest->getBody();
        $contents = $result->getContents();
        $contents = json_decode($contents, JSON_NUMERIC_CHECK);

        return $contents;
    }

    public function listCasts(Request $request)
    {
        try {
            $params = ['cast_new' => 1];
            if ($request->all()) {
                !$request->prefecture_id ?: $params['prefecture_id'] = $request->prefecture_id;
                !$request->class_id ?: $params['class_id'] = $request->class_id;
                !$request->point ?: $params['min_point'] = explode(',', $request->point)[0];
                !$request->point ?: $params['max_point'] = explode(',', $request->point)[1];
            }

            $contents = $this->getApi('/api/v1/casts', $params);
            $casts = $contents['data'];

            return view('web.users.list_casts', compact('casts'));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
    }

    public function loadMoreListCasts(Request $request)
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

            $apiRequest = $client->request('GET', $request->next_page);

            $result = $apiRequest->getBody();
            $contents = $result->getContents();
            $contents = json_decode($contents, JSON_NUMERIC_CHECK);

            $casts = $contents['data'];

            return [
                'next_page' => $casts['next_page_url'],
                'view' => view('web.users.load_more_list_casts', compact('casts'))->render(),
            ];
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
    }

    public function listCastsFavorite(Request $request)
    {
        try {
            $params = [
                'favorited' => 1,
                'cast_new' => 1,
            ];
            if ($request->all()) {
                !$request->prefecture_id ?: $params['prefecture_id'] = $request->prefecture_id;
                !$request->point ?: ($params['min_point'] = explode(',', $request->point)[0]);
                !$request->point ?: $params['max_point'] = explode(',', $request->point)[1];
                !$request->class_id ?: $params['class_id'] = $request->class_id;
            }

            $contents = $this->getApi('/api/v1/casts', $params);
            $favorites = $contents['data'];

            return view('web.users.list_casts_favorite', compact('favorites'));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
    }

    public function loadMoreListCastsFavorite(Request $request)
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

            $apiRequest = $client->request('GET', $request->next_page);

            $result = $apiRequest->getBody();
            $contents = $result->getContents();
            $contents = json_decode($contents, JSON_NUMERIC_CHECK);

            $favorites = $contents['data'];

            return [
                'next_page' => $favorites['next_page_url'],
                'view' => view('web.users.load_more_list_casts_favorite', compact('favorites'))->render(),
            ];
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
    }

    public function search()
    {
        try {
            $params = [
                'filter' => 'supported',
            ];
            $prefectures = $this->getApi('/api/v1/prefectures', $params);
            $prefectures = $prefectures['data'];

            $castClasses = $this->getApi('/api/v1/cast_classes');
            $castClasses = $castClasses['data'];

            return view('web.users.cast_search', compact('castClasses', 'prefectures'));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
    }

    public function show($id)
    {
        try {
            $contents = $this->getApi('/api/v1/users/' . $id);
            $cast = $contents['data'];

            return view('web.users.show', compact('cast'));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
    }
}
