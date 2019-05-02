<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TimeLineResource;
use App\Http\Resources\TimelineFavoritesResource;
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

        $timeLine = TimeLine::where('user_id', $id)->where('hidden', false)->paginate(10);

        return $this->respondWithData(TimeLineResource::collection($timeLine));
    }

    public function show($id) {
        $timeLine = TimeLine::find($id);

        return $this->respondWithData(TimeLineResource::make($timeLine));
    }

    public function listTimelineFavorites($id) {
        $timeLine = TimeLine::find($id);
        $timelineFavorites = $timeLine->favorites;

        return $this->respondWithData(TimelineFavoritesResource::collection($timelineFavorites));

    }
}
