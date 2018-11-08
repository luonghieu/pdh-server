<?php

namespace App\Http\Controllers\Api;

use App\Cast;
use App\Http\Resources\CastResource;
use Illuminate\Http\Request;

class CastController extends ApiController
{
    public function index(Request $request)
    {
        $rules = [
            'per_page' => 'numeric|min:1',
            'min_point' => 'numeric',
            'max_point' => 'numeric|required_with:min_point',
            'favorited' => 'boolean',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $params = $request->only([
            'working_today',
            'class_id',
            'height',
            'body_type_id',
            'device',
        ]);

        $user = $this->guard()->user();
        if (!$user->status) {
            return $this->respondErrorMessage(trans('messages.freezing_account'), 403);
        }

        $casts = Cast::query();
        foreach ($params as $key => $value) {
            if (!(3 == $request->device)) {
                $casts->where($key, $value);
            }
        }

        if ($request->favorited) {
            $casts->whereHas('favoriters', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        if (isset($request->min_point) && isset($request->max_point)) {
            $min = $request->min_point;
            $max = $request->max_point;
            $casts->whereBetween('users.cost', [$min, $max]);
        }

        $casts = $casts->whereDoesntHave('blockers', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereDoesntHave('blocks', function ($q) use ($user) {
            $q->where('blocked_id', $user->id);
        })->active();

        if ($request->device) {
            $casts = $casts->orderByDesc('working_today')
                ->orderBy('rank')
                ->orderByDesc('created_at')
                ->orderByDesc('last_active_at')
                ->limit(10)->get();
        } elseif ($request->latest) {
            $casts = $casts->orderByDesc('users.created_at')
                ->orderBy('rank')
                ->paginate($request->per_page)
                ->appends($request->query());
        } else {
            $casts = $casts->leftJoin('cast_order as co', 'co.user_id', '=', 'users.id')
                ->groupBy('users.id')
                ->orderBy('rank')
                ->orderBy('last_active_at', 'DESC')
                ->orderByDesc('co.created_at')
                ->select('users.*')
                ->paginate($request->per_page)
                ->appends($request->query());
        }

        return $this->respondWithData(CastResource::collection($casts));
    }
}
