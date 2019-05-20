<?php

namespace App\Http\Resources;

use App\Traits\ResourceResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\Resource;

class CastOfferResource extends Resource
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
            'class_id' => $this->cast_class_id,
            'guest' => $this->guest,
            'address' => $this->address,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'duration' => $this->duration,
            'created_at' => $this->created_at,
        ]);
    }
}
