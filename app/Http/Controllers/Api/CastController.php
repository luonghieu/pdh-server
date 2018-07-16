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
            'max_point' => 'numeric|required_with:min_point'
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $params = $request->only([
            'favorited',
            'working_today',
            'prefecture_id',
            'class_id',
            'height',
            'body_type_id',
        ]);

        $casts = Cast::query();
        $user = $this->guard()->user();

        foreach ($params as $key => $value) {
            if ($key == 'favorited') {
                $casts->whereHas('favorites', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            } else {
                $casts->where($key, $value);
            }
        }

        if (isset($request->min_point) && isset($request->max_point)) {
            $min = $request->min_point;
            $max = $request->max_point;
            $casts->whereBetween('cost', [$min, $max]);
        }
        $casts = $casts->latest()->active()->paginate($request->per_page)->appends($request->query());

        return $this->respondWithData(CastResource::collection($casts));
    }
}
