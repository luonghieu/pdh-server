<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NotificationResource;

class NotificationController extends ApiController
{
    public function show($id)
    {
        $user = $this->guard()->user();

        $notify = $user->notifications()->find($id);

        if (!$notify) {
            return $this->respondErrorMessage(trans('messages.notify_not_found'), 404);
        }

        try {
            if (!$notify->read_at) {
                $notify->markAsRead();
            }
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }

        return $this->respondWithData(NotificationResource::make($notify));
    }
}
