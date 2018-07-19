<?php

namespace App\Http\Resources;

use App\Traits\ResourceResponse;
use Illuminate\Http\Resources\Json\Resource;

class MessageResource extends Resource
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
            'id' => $this->id,
            'room_id' => $this->room_id,
            'user_id' => $this->user_id,
            'user' => $this->user->is_guest ? new GuestResource($this->user) : new CastResource($this->user),
            'message' => $this->message,
            'image' => $this->image,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
