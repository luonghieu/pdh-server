<?php

namespace App\Http\Controllers\Admin\Chat_Room;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatRoomController extends Controller
{

    public function index()
    {

        $user = Auth::user();
        $user_id = $user->id;
        $token = JWTAuth::fromUser($user);
        return view('admin.chatroom.index', compact('token', 'user_id'));
    }


}
