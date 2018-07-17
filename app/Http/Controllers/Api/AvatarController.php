<?php

namespace App\Http\Controllers\Api;

use App\Services\LogService;

class AvatarController extends ApiController
{
    public function setAvatarDefault($id)
    {
        $user = $this->guard()->user();

        $avatar = $user->avatars->find($id);

        if (!$avatar) {
            return $this->respondErrorMessage(trans('messages.avatar_not_found'), 404);
        }

        try {
            $avatar->is_default = true;
            $avatar->save();
            $user->avatars()->where('id', '!=', $id)->update(['is_default' => false]);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.set_avatar_default_success'));
    }
}
