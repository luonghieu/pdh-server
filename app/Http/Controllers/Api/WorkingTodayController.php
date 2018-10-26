<?php

namespace App\Http\Controllers\Api;

use App\User;

class WorkingTodayController extends ApiController
{
    public function update()
    {
        $user = User::find($this->guard()->user()->id);
        if (!$user->status) {
            return $this->respondErrorMessage(trans('messages.freezing_account'), 403);
        }

        $user->working_today = !$user->working_today;
        $user->update();

        $workingToday = ($user->working_today) ? 1 : 0;

        return $this->respondWithData(['working_today' => $workingToday]);
    }
}
