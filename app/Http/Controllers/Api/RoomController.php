<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoomType;
use App\Enums\UserType;
use App\Http\Resources\RoomResource;
use App\Room;
use App\Services\LogService;
use App\User;
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

    public function getUsers()
    {

        $rooms = Room::active()->where('type', RoomType::SYSTEM);

        $rooms = $rooms->with(['users' => function ($query) {
            $query->whereNotIn('type', [Usertype::ADMIN]);
        }])->orderBy('updated_at', 'DESC')->get();

        return $this->respondWithData(RoomResource::collection($rooms));
    }
}
