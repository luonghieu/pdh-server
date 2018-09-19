<?php

namespace App\Http\Controllers;

use App\Helpers\GuzzleHelper;
use Auth;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use JWTAuth;

class ProfileController extends Controller
{
    public function __construct(GuzzleHelper $client)
    {
        $this->client = $client;
    }

    public function index()
    {
        $user = Auth::guard()->user();
        $token = JWTAuth::fromUser($user);

        $contents = $this->client->get(route('auth.me'), [], $token);

        if ($contents) {
            $profile = $contents['data'];

            return view('web.profile.index', compact('profile'));
        }
    }
}
