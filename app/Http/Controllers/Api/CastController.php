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

        if ($request->order) {
            $casts = $casts->orderByDesc('working_today');
        }

        if ($request->favorited) {
            $casts->whereHas('favoriters', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        if ($request->search) {
            $search = $request->search;
            $casts->where(function ($query) use ($search) {
                $query->where('nickname', 'like', "%$search%")
                    ->orWhere('users.id', $search);
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

        if ($request->device || $request->working_today) {
            $casts = $casts->orderByDesc('working_today')
                ->orderBy('rank')
                ->orderByDesc('created_at')
                ->orderByDesc('last_active_at');

            if ($request->device == 3) {
                $casts = $casts->limit(10)->get();
            } else {
                $casts = $casts->paginate(10)->appends($request->query());
            }
        } elseif ($request->latest) {
            $casts = $casts->orderBy('rank')
                ->orderByDesc('users.created_at')
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
