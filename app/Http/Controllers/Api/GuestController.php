<?php

namespace App\Http\Controllers\Api;

use App\Guest;
use App\Http\Resources\GuestResource;
use Illuminate\Http\Request;

class GuestController extends ApiController
{
    public function index(Request $request)
    {

        $guests = Guest::query();

        if ($request->filter == 'favorited') {
            $guests->has('favorites');
        }

        $casts = $guests->latest()->paginate($request->per_page)->appends($request->query());

        return $this->respondWithData(GuestResource::collection($casts));
    }
}
