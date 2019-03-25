<?php

namespace App\Http\Controllers\Api\Cast;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\ShiftResource;
use App\Services\LogService;
use Illuminate\Http\Request;
class ShiftController extends ApiController
{

    public function index(Request $request)
    {
        $user = $this->guard()->user();
        $from = now()->copy()->startOfDay();
        $to = now()->copy()->addDays(14)->startOfDay();
        $shifts = $user->shifts()->whereBetween('date', [$from, $to])->get();

        return $this->respondWithData(ShiftResource::collection($shifts));
    }

    public function update(Request $request) {
        $user = $this->guard()->user();
        try {
            $user->shifts()->sync($request->shifts);

            return $this->respondWithNoData(trans('messages.update_shifts_success'));
        }catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return $this->respondServerError();
        }
    }
}
