<?php

namespace App\Http\Controllers\Api;

use App\User;

class FavoriteController extends ApiController
{
    public function favorite($id)
    {
        $user = $this->guard()->user();

        if (!User::find($id)) {
            return $this->respondErrorMessage(trans('messages.user_not_found'), 404);
        }

        if ($user->isFavoritedUser($id)) {
            $user->favorites()->detach($id);

            return $this->respondWithNoData(trans('messages.unfavorite_success'));
        }

        $user->favorites()->attach($id);

        return $this->respondWithNoData(trans('messages.favorite_success'));
    }
}
