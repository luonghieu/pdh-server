<?php

namespace App\Http\Controllers\Api\Cast;

use App\Http\Controllers\Api\ApiController;
use App\Services\LogService;
use Illuminate\Http\Request;

class ShiftController extends ApiController
{
    public function update(Request $request) {
        $user = $this->guard()->user();
        try {
            $user->shifts()->syncWithoutDetaching($request->shifts);

            return $this->respondWithNoData(trans('messages.update_shifts_success'));
        }catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return $this->respondServerError();
        }
    }
}
