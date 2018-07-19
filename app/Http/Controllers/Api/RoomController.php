<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Room;
use App\Enums\RoomType;
use App\Http\Resources\RoomResource;
use App\Services\LogService;

class RoomController extends ApiController
{
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'user_id' => 'required'
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
            $room = $user->rooms()->where('type', '=', RoomType::DIRECT)->whereHas('users',
                function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->first();
            if (!$room) {
                $room = new Room();
                $room->type = $type;
                $room->save();
                $room->users()->attach([$userId, $user->id]);
                return $this->respondWithData(RoomResource::make($room));
            }
            return $this->respondWithData(RoomResource::make($room));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return $this->respondServerError();
        }
    }
}
