<?php

namespace App\Http\Controllers\Api;

use App\User;

class WorkingTodayController extends ApiController
{
    public function update()
    {
        $user = User::find($this->guard()->user()->id);

        $user->working_today = !$user->working_today;
        $user->update();

        return $this->respondWithNoData(trans('messages.update_working_today_success'));
    }
}
