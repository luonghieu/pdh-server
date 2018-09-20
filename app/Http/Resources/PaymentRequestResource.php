<?php

namespace App\Http\Resources;

use App\Traits\ResourceResponse;
use Auth;
use Illuminate\Http\Resources\Json\Resource;

class PaymentRequestResource extends Resource
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
        $isCast = Auth::user()->is_cast;
        $castPercent = config('common.cast_percent');

        return $this->filterNull([
            'id' => $this->id,
            'cast_id' => $this->cast_id,
            'guest_id' => $this->guest_id,
            'order_id' => $this->order_id,
            'order_time' => $this->order_time,
            'extra_time' => $this->extra_time,
            'order_point' => $this->when($isCast, $castPercent * $this->order_point, $this->order_point),
            'extra_point' => $this->when($isCast, $castPercent * $this->extra_point, $this->extra_point),
            'allowance_point' => $this->when($isCast, $castPercent * $this->allowance_point, $this->allowance_point),
            'fee_point' => $this->when($isCast, $castPercent * $this->fee_point, $this->fee_point),
            'total_point' => $this->when($isCast, $castPercent * $this->total_point, $this->total_point),
            'status' => $this->status,
            'order' => OrderResource::make($this->whenLoaded('order')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
