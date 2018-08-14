<?php

namespace App\Http\Resources;

use App\Traits\ResourceResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class CastOrderResource extends JsonResource
{
    use ResourceResponse;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $isCast = Auth::user()->is_cast;
        $castPercent = config('common.cast_percent');

        return $this->filterNull([
            'order_time' => $this->order_time,
            'temp_point' => $this->when($isCast, $castPercent * $this->temp_point, $this->temp_point),
            'extra_time' => $this->extra_time,
            'order_point' => $this->order_point,
            'extra_point' => $this->extra_point,
            'allowance_point' => $this->allowance_point,
            'fee_point' => $this->fee_point,
            'total_point' => $this->total_point,
            'type' => $this->type,
            'status' => $this->status,
            'guest_rated' => $this->guest_rated,
            'cast_rated' => $this->cast_rated,
            'is_thanked' => $this->is_thanked,
            'accepted_at' => $this->accepted_at,
            'canceled_at' => $this->canceled_at,
            'started_at' => $this->started_at,
            'stopped_at' => $this->stopped_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
