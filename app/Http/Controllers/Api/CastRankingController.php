<?php

namespace App\Http\Controllers\Api;

use App\CastRanking;
use App\Http\Resources\CastRankingResource;
use App\User;

class CastRankingController extends ApiController
{
    public function index()
    {
        $castRankings = CastRanking::get()->pluck('user_id');
        $users = User::whereIn('id', $castRankings)
            ->orderBy('total_point', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
        return $this->respondWithData(CastRankingResource::collection($users));
    }
}
