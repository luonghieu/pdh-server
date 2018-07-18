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
        $userIds = [$userId, $user];
        try {
            $findRom = Room::whereHas('users', function ($query) use ($userIds) {
                $query->whereIn('user_id', $userIds);
            })->where('type','=', RoomType::DIRECT)->get()->pluck('id')->first();
            if (!$findRom) {
                $room = new Room();
                $room->type = $type;
                $room->save();
                $room->users()->attach($userId);
                $room->users()->attach($user);
                return $this->respondWithData(RoomResource::make($room));
            }
            return $this->respondWithData(RoomResource::make($findRom));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
        }
    }
}
