<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoomType;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Room;
use App\User;
use DB;
use http\Url;
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

        $unReads = DB::table('message_recipient')
            ->where('user_id', 1)
            ->where('is_show', true)
            ->whereNull('read_at')
            ->select('room_id', DB::raw('count(*) as total'))
            ->groupBy('room_id')->get();

        $rooms = DB::table('rooms')->where('is_active', true)
            ->where('rooms.type', RoomType::SYSTEM)
            ->join('users', 'rooms.owner_id', '=', 'users.id')
            ->leftJoin('avatars', function ($j) {
                $j->on('avatars.user_id', '=', 'users.id')
                    ->where('is_default', true);
            })
            ->where('users.deleted_at', null)
            ->select('rooms.*', 'users.type As user_type', 'users.gender', 'users.nickname', 'avatars.thumbnail')
            ->orderBy('users.type', 'DESC')->orderBy('users.updated_at', 'DESC')
            ->paginate(100);

        $rooms = json_encode($rooms->items(), true);
        $storagePath = \Storage::url('');
        $baseUrl = \URL::to('/');

        return view('admin.chatroom.index', compact('token', 'userId', 'rooms', 'unReads', 'storagePath', 'baseUrl'));
    }
}
