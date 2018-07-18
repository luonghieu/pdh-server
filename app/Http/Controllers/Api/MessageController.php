<?php

namespace App\Http\Controllers\Api;

use App\Message;

class MessageController extends ApiController
{
    public function delete($id)
    {
        $user = $this->guard()->user();

        $message = $user->messages()->find($id);

        if (!$message) {
            return $this->respondErrorMessage(trans('messages.message_exits'), 409);
        }

        $message->delete($id);

        return $this->respondWithNoData(trans('messages.delete_message_success'));
    }
}
