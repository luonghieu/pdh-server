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

        $workingToday = ($user->working_today) ? 1 : 0;

        return $this->respondWithData(['working_today' => $workingToday]);
    }
}
