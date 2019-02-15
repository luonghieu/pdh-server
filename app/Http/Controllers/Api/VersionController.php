<?php

namespace App\Http\Controllers\Api;

class VersionController extends ApiController
{
    public function index()
    {
        $data = [
            'android' => env('ANDROID_VERSION'),
            'ios' => env('IOS_VERSION'),
        ];

        return $this->respondWithData($data);
    }
}
