<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoomType;
use App\Http\Resources\RoomResource;
use App\Room;
use App\Services\LogService;
use Illuminate\Http\Request;

class RoomController extends ApiController
{
    public function index(Request $request)
    {
        $rules = [
            'per_page' => 'numeric|min:1',
            'favorited' => 'boolean',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();

        $rooms = Room::active()->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });

        if ($request->favorited) {
            $isFavoritedIds = $user->favorites()->pluck('favorited_id')->toArray();

            $rooms->where('type', RoomType::DIRECT)->whereHas('users', function ($query) use ($isFavoritedIds) {
                $query->whereIn('user_id', $isFavoritedIds);
            });
        }

        $rooms = $rooms->with('latestMessage', 'users')->orderBy('updated_at', 'DESC')->paginate($request->per_page)->appends($request->query());

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
        $userId = $request->user_id;
        $type = $request->type;

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
}
