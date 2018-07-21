<?php

namespace App\Http\Resources;

use App\Traits\ResourceResponse;
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
            'prefecture_id' => $this->prefecture_id,
            'address' => $this->address,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'duration' => $this->duration,
            'extra_time' => $this->extra_time,
            'night_time' => $this->night_time,
            'total_time' => $this->total_time,
            'total_cast' => $this->total_cast,
            'temp_point' => $this->temp_point,
            'fee_point' => $this->fee_point,
            'total_point' => $this->total_point,
            'class_id' => $this->class_id,
            'type' => $this->type,
            'status' => $this->status,
            'actual_started_at' => $this->actual_started_at,
            'actual_ended_at' => $this->actual_ended_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'tags' => TagCollection::make($this->whenLoaded('tags')),
            'casts' => UserCollection::make($this->whenLoaded('casts')),
            'user' => new UserResource($this->whenLoaded('user')),
        ]);
    }
}
