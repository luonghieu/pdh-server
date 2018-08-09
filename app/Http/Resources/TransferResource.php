<?php

namespace App\Http\Resources;

use App\Traits\ResourceResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
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
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'transfered_at' => $this->transfered_at,
            'amount' => $this->amount,
            'user' => UserResource::make($this->whenLoaded('user')),
            'order' => OrderResource::make($this->whenLoaded('order')),
        ];
    }
}
