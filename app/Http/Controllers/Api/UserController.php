<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Favorite;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\CastResource;
use App\Http\Resources\GuestResource;

class UserController extends ApiController
{

    public function show(Request $request)
    {
        $userId = $request->id;
        $user = User::find($userId);
        if (!$user) {
            return $this->respondErrorMessage(trans('messages.user_not_found'), 404);
        }
        if (UserType::CAST == $user->type) {
            return $this->respondWithData(new CastResource($user));
        }
        return $this->respondWithData(new GuestResource($user));
    }
}
