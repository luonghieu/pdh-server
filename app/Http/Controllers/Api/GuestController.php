<?php

namespace App\Http\Controllers\Api;

use App\Guest;
use App\Http\Resources\GuestResource;
use Illuminate\Http\Request;

class GuestController extends ApiController
{
    public function index(Request $request)
    {
        $rules = [
            'per_page' => 'numeric|min:1',
            'favorited' => 'boolean',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $guests = Guest::query();
        $user = $this->guard()->user();

        if ($request->favorited) {
            $guests->whereHas('favoriters', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        $casts = $guests->latest()->active()->WhereDoesntHave('blockers', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->WhereDoesntHave('blocks', function ($q) use ($user) {
            $q->where('blocked_id', $user->id);
        })->paginate($request->per_page)->appends($request->query());

        return $this->respondWithData(GuestResource::collection($casts));
    }
}
