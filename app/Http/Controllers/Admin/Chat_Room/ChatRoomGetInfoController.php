<?php

namespace App\Http\Controllers\Admin\Chat_Room;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Room;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatRoomGetInfoController extends Controller
{
    public function getToken()
    {

        $user = Auth::user();
        $user_id = $user->id;
        $token = JWTAuth::fromUser($user);
        return response()->json(['token' => $token, 'user_id' => $user_id]);

    }

}
