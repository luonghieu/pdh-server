<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoomType;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Room;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatRoomController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $token = JWTAuth::fromUser($user);

        $rooms = Room::active()->where('type', RoomType::SYSTEM)
            ->with(['users' => function ($query) {
            $query->whereNotIn('type', [Usertype::ADMIN]);
        }])->orderBy('updated_at', 'DESC')->get();

        $unReads = DB::table('message_recipient')
            ->where('user_id', 1)
            ->where('is_show', true)
            ->whereNull('read_at')
            ->select('room_id', DB::raw('count(*) as total'))
            ->groupBy('room_id')->get();

        return view('admin.chatroom.index', compact('token', 'userId', 'rooms', 'unReads'));
    }


}
