<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatRoomController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
        $token = JWTAuth::fromUser($user);
        return view('admin.chatroom.index', compact('token', 'userId'));
    }


}
