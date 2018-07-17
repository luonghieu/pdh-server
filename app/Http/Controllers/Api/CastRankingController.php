<?php

namespace App\Http\Controllers\Api;


use App\CastRanking;
use App\Http\Resources\CastRankingResource;

class CastRankingController extends ApiController
{
    public function index()
    {
        $castRankings = CastRanking::all();
        return $this->respondWithData(CastRankingResource::collection($castRankings));
    }
}
