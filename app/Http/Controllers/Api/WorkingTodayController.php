<?php

namespace App\Http\Controllers\Api;

use App\User;
use Carbon\Carbon;

class WorkingTodayController extends ApiController
{
    public function update()
    {
        $user = User::find($this->guard()->user()->id);
        if (!$user->status) {
            return $this->respondErrorMessage(trans('messages.freezing_account'), 403);
        }
        $today = Carbon::today();
        $user->working_today = !$user->working_today;
        $shiftToday = $user->shifts()->where('date', $today)->first();
        $shiftToday->pivot->day_shift = $user->working_today;
        $shiftToday->pivot->night_shift = $user->working_today;
        $shiftToday->pivot->save();
        $user->update();

        $workingToday = ($user->working_today) ? 1 : 0;

        return $this->respondWithData(['working_today' => $workingToday]);
    }
}
