<?php

namespace App\Http\Controllers;

use App\Cast;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $token = '';

        if (Auth::check()) {
            $token = JWTAuth::fromUser(Auth::user());
        }

        return view('web.index', compact('token'));
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('web.index');
    }
}
