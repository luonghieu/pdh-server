<?php

namespace App\Http\Resources;

use App\Traits\ResourceResponse;
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
        return $this->filterNull([
            'order_time' => $this->order_time,
            'extra_time' => $this->extra_time,
            'order_point' => $this->order_point,
            'extra_point' => $this->extra_point,
            'allowance_point' => $this->allowance_point,
            'fee_point' => $this->fee_point,
            'total_point' => $this->total_point,
            'type' => $this->type,
            'status' => $this->status,
            'accepted_at' => $this->accepted_at,
            'canceled_at' => $this->canceled_at,
            'started_at' => $this->started_at,
            'stopped_at' => $this->stopped_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
