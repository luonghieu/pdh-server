<?php

namespace App\Http\Controllers\Api;

use Cache;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class PostCodeController extends ApiController
{
    protected $client;

    public function __construct()
    {
        $this->client = app(Client::class);
    }

    public function find(Request $request)
    {
        $validator = validator($request->only('post_code'), ['post_code' => 'required']);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        //$pattern = '/^([1-2][0-9]|3[3-6]|5[3-9]|6[5-7])\d{1}[-]?\d{4}$/';
        //only Tokyo

        $subject = $request->post_code;
        $count = strlen($subject);

        if ($count < 7) {
            return $this->respondErrorMessage(trans('messages.postcode_invalid'), 400);
        }

//        $pattern = '/^(1[0-9]|20)\d{1}[-]?\d{4}$/';
//
//        if (preg_match($pattern, $subject) == false) {
//            return $this->respondErrorMessage(trans('messages.postcode_not_support'), 422);
//        }

        $address = '';
        $cacheKey = "post_code_{$request->post_code}";

        if (!Cache::has($cacheKey)) {
            $response = $this->client->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
                'query' => [
                    'address' => $request->post_code,
                    'language' => 'ja',
                    'sensor' => false,
                    'key' => env('GOOGLE_GEO_KEY')
                ],
            ]);

            if ($response->getStatusCode() == 200) {
                $content = json_decode($response->getBody()->getContents(), true);

                if ('OK' == $content['status'] && isset($content['results'][0]['address_components'])) {
                    $address = $content['results'][0]['address_components'];

                    // cache post code info for 3 months
                    $expiresAt = now()->addMonths(3);
                    Cache::put($cacheKey, $address, $expiresAt);
                }
            }
        } else {
            $address = Cache::get($cacheKey);
        }

        if (!$address) {
            return $this->respondErrorMessage(trans('messages.postcode_error'));
        }

        return $this->respondWithData(['address' => $address]);
    }
}
