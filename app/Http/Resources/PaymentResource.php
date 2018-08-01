<?php

namespace App\Http\Resources;

use App\Http\Resources\CastResource;
use App\Http\Resources\OrderResource;
use App\Traits\ResourceResponse;
use Illuminate\Http\Resources\Json\Resource;

class PaymentResource extends Resource
{
    use ResourceResponse;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->filterNull([
            'id' => $this->pivot->id,
            'user_id' => $this->pivot->user_id,
            'order_id' => $this->pivot->order_id,
            'order_time' => $this->pivot->order_time,
            'extra_time' => $this->pivot->extra_time,
            'order_point' => $this->pivot->order_point,
            'extra_point' => $this->pivot->extra_point,
            'allowance_point' => $this->pivot->allowance_point,
            'fee_point' => $this->pivot->fee_point,
            'total_point' => $this->pivot->total_point,
            'night_time' => $this->pivot->night_time,
            'total_time' => $this->pivot->total_time,
            'type' => $this->pivot->type,
            'status' => $this->pivot->status,
            'accepted_at' => $this->pivot->accepted_at,
            'canceled_at' => $this->pivot->canceled_at,
            'started_at' => $this->pivot->started_at,
            'stopped_at' => $this->pivot->stopped_at,
            'cast' => CastResource::make($this),
            'order' => OrderResource::make($this->pivot->order_id),
        ]);
    }
}
