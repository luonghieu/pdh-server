<?php

namespace App\Http\Resources;

use App\Cast;
use App\Http\Resources\CastResource;
use App\Traits\ResourceResponse;
use Illuminate\Http\Resources\Json\Resource;

class OfferResource extends Resource
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
            'prefecture_id' => $this->prefecture_id,
            'comment' => $this->comment,
            'address' => $this->address,
            'date' => $this->date,
            'start_time_from' => $this->start_time_from,
            'start_time_to' => $this->start_time_to,
            'expired_date' => $this->start_time_to,
            'duration' => $this->duration,
            'total_time' => $this->total_time,
            'total_cast' => $this->total_cast,
            'temp_point' => $this->temp_point,
            'total_point' => $this->total_point,
            'class_id' => $this->class_id,
            'type' => 2,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'casts' => CastResource::collection(Cast::whereIn('id', $this->cast_ids)->get()),
            'deleted_at' => $this->deleted_at,
        ]);
    }
}
