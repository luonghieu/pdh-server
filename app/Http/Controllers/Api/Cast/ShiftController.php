<?php

namespace App\Http\Controllers\Api\Cast;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\ShiftResource;
use Illuminate\Http\Request;

class ShiftController extends ApiController
{

    public function index(Request $request)
    {
        $user = $this->guard()->user();
        $from = now();
        $to = now()->copy()->addDays(14);
        $shifts = $user->shifts()->whereBetween('date', [$from, $to])->get();

        return $this->respondWithData(ShiftResource::collection($shifts));
    }
}
