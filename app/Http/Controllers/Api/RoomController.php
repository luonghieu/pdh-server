<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoomType;
use App\Http\Resources\RoomResource;
use App\Room;
use App\User;
use Illuminate\Http\Request;

class RoomController extends ApiController
{
    public function index(Request $request)
    {
        $rules = [
            'per_page' => 'numeric|min:1',
            'favorited' => 'numeric',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();

        $rooms = Room::active()->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });

        if (isset($request->favorited) && 1 == $request->favorited) {
            $isFavoritedIds = $user->favorites()->pluck('favorited_id')->toArray();

            $rooms->where('type', RoomType::DIRECT)->whereHas('users', function ($query) use ($isFavoritedIds) {
                $query->whereIn('user_id', $isFavoritedIds);
            });
        }

        $rooms = $rooms->with('latestMessage', 'users')->orderBy('updated_at', 'DESC')->paginate($request->per_page)->appends($request->query());

        return $this->respondWithData(RoomResource::collection($rooms));
    }
}
