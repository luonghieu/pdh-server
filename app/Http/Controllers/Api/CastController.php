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
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $params = $request->except(['order_by']);
        $casts = Cast::query();
        foreach ($params as $key => $value) {
            if ($key == 'favorited') {
                $casts->has('favorites');
            } else {
                $casts->where($key, $value);
            }
        }

        $casts = $casts->latest()->paginate($request->per_page)->appends($request->query());

        return $this->respondWithData(CastResource::collection($casts));
    }
}
