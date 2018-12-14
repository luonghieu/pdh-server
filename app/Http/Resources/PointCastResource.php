<?php

namespace App\Http\Resources;

use App\Traits\ResourceResponse;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class PointCastResource extends Resource
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
        $order = OrderResource::make($this->whenLoaded('order'));

        return $this->filterNull([
            'id' => $this->id,
            'cast_id' => $this->user_id,
            'order_id' => $this->order_id,
            'is_admin' => $this->is_adjusted ? 1 : 0,
            'nickname' => $this->is_adjusted ? 'Cheers運営局' : $order->resource->user->nickname,
            'point' => $this->point,
            'type' => $this->type,
            'date' => $this->is_adjusted ? Carbon::parse($this->created_at)->format('Y-m-d') : Carbon::parse($order->resource->date)->format('Y-m-d'),
            'order' => is_null($order->resource) ? '' : $order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
