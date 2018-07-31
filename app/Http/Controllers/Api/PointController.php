<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PointResource;
use Illuminate\Http\Request;

class PointController extends ApiController
{
    public function points(Request $request)
    {
        $user = $this->guard()->user();

        $points = $user->points()->latest()->paginate($request->per_page)->appends($request->query());

        return $this->respondWithData(PointResource::collection($points));
    }
}
