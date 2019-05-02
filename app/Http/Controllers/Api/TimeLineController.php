<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TimeLineResource;
use App\TimeLine;
use Illuminate\Http\Request;

class TimeLineController extends ApiController
{
    public function index(Request $request)
    {
        $user = $this->guard()->user();

        $id = $user->id;
        if ($request->user_id) {
            $id = $request->user_id;
        }

        $timeLine = TimeLine::where('user_id', $id)->where('hidden', false)->withCount('favorites')->paginate(10);

        return $this->respondWithData(TimeLineResource::collection($timeLine));
    }
}
