<?php

namespace App\Http\Resources;

use App\Http\Resources\PricingResource;
use App\Traits\ResourceResponse;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class OrderResource extends Resource
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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'plan_id' => $this->plan_id,
            'date' => $this->date,
            'pricing_id' => $this->pricing_id,
            'pricing' => new PricingResource($this->whenLoaded('pricing')),
            'start_time' => Carbon::parse($this->start_time)->format('H:i'),
            'end_time' => Carbon::parse($this->end_time)->format('H:i'),
            'address' => $this->address,
            'total_cast' => $this->total_cast,
            'points' => $this->points,
            'status' => $this->status,
            'accept_time' => Carbon::parse($this->accept_time)->format('Y-m-d H:i'),
            'cancel_time' => Carbon::parse($this->cancel_time)->format('Y-m-d H:i'),
            'actual_start_time' => Carbon::parse($this->actual_start_time)->format('Y-m-d H:i'),
            'actual_end_time' => Carbon::parse($this->actual_end_time)->format('Y-m-d H:i'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
