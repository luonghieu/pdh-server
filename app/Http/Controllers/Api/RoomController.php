<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoomType;
use App\Enums\UserType;
use App\Http\Resources\RoomResource;
use App\Message;
use App\Room;
use App\Services\LogService;
use App\User;
use DB;
use Illuminate\Http\Request;

class RoomController extends ApiController
{
    public function index(Request $request)
    {
        $rules = [
            'per_page' => 'numeric|min:1',
            'favorited' => 'boolean',
            'nickname' => 'max:20',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();
        if (!$user->status) {
            return $this->respondErrorMessage(trans('messages.freezing_account'), 403);
        }

        $rooms = Room::active()->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });

        $nickName = $request->nickname;
        if ($nickName) {
            $rooms->whereHas('users', function ($query) use ($nickName) {
                $query->where('users.nickname', 'like', "%$nickName%");
            });
        }

        if ($request->favorited) {
            $isFavoritedIds = $user->favorites()->pluck('favorited_id')->toArray();

            $rooms->where('type', RoomType::DIRECT)->whereHas('users', function ($query) use ($isFavoritedIds) {
                $query->whereIn('user_id', $isFavoritedIds);
            });
        }

        $rooms = $rooms->with('latestMessage', 'users')->orderBy('updated_at', 'DESC')->paginate($request->per_page)->appends($request->query());

        if ('html' == $request->response_type) {
            $rooms = $this->respondWithData(RoomResource::collection($rooms));
            $rooms = $rooms->getData()->data;

            if (!$rooms->data) {
                return view('web.rooms.no_room');
            }
            return view('web.rooms.content-room', compact('rooms'));
        }

        return $this->respondWithData(RoomResource::collection($rooms));
    }

    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();
        if (!$user->status) {
            return $this->respondErrorMessage(trans('messages.freezing_account'), 403);
        }

        $userId = $request->user_id;
        $type = $request->type;

        if (User::find($userId)->type == $user->type) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        if (!$type) {
            $type = RoomType::DIRECT;
        }

        try {
            $room = $user->rooms()->direct()->whereHas('users', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->first();

            if (!$room) {
                $room = new Room;
                $room->type = $type;
                $room->is_active = true;
                $room->owner_id = $user->id;
                $room->save();

                $room->users()->attach([$userId, $user->id]);
            }

            return $this->respondWithData(RoomResource::make($room));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }

    public function getRooms(Request $request)
    {
        if ($request->search) {
            $search = $request->search;
            $rooms = DB::table('rooms')->where('is_active', true)
                ->where('rooms.type', RoomType::SYSTEM)
                ->join('users', function ($j) use ($search) {
                    $j->on('rooms.owner_id', '=', 'users.id')
                        ->where(function ($w) use ($search) {
                            $w->Where('users.id', 'like', '%' . $search . '%')
                                ->orWhere('users.nickname', 'like', '%' . $search . '%');
                        });
                })
                ->leftJoin('avatars', function ($j) {
                    $j->on('avatars.user_id', '=', 'users.id')
                        ->where('is_default', true);
                })
                ->where('users.deleted_at', null)
                ->select('rooms.*', 'users.type As user_type', 'users.gender', 'users.nickname', 'avatars.thumbnail')
                ->orderBy('users.type', 'DESC')->orderBy('users.updated_at', 'DESC')
                ->get();
        } else {
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
                ->paginate(100)->appends($request->query());
        }

        return response()->json($rooms, 200);
    }

    public function getRoom(Request $request) {
        $id = $request->id;
        $room = DB::table('rooms')->where('is_active', true)
            ->where('rooms.type', RoomType::SYSTEM)
            ->where('rooms.id', $id)
            ->join('users', 'rooms.owner_id', '=', 'users.id')
            ->leftJoin('avatars', function ($j) {
                $j->on('avatars.user_id', '=', 'users.id')
                    ->where('is_default', true);
            })
            ->where('users.deleted_at', null)
            ->select('rooms.*', 'users.type As user_type', 'users.gender', 'users.nickname', 'avatars.thumbnail')
            ->orderBy('users.type', 'DESC')->orderBy('users.updated_at', 'DESC')
            ->first();

        return response()->json($room, 200);
    }

    public function getAdminUnreadMessages()
    {
        $messages = DB::table('message_recipient')
            ->where('user_id', 1)
            ->where('is_show', true)
            ->whereNull('read_at')
            ->select('room_id', DB::raw('count(*) as total'))
            ->groupBy('room_id')->get();

        return response()->json($messages, 200);
    }
}
