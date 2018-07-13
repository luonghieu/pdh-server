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
        $user = $this->guard()->user();

        if ($request->filter == 'favorited') {
            $guests->whereHas('favorites', function($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        $casts = $guests->latest()->paginate($request->per_page)->appends($request->query());

        return $this->respondWithData(GuestResource::collection($casts));
    }
}
