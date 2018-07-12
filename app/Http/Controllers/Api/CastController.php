<?php

namespace App\Http\Controllers\Api;

use App\Cast;
use App\Http\Resources\CastResource;
use Illuminate\Http\Request;

class CastController extends ApiController
{
    public function index(Request $request)
    {
        $casts = Cast::latest()->paginate($request->per_page)->appends($request->query());

        return $this->respondWithData(CastResource::collection($casts));
    }
}
