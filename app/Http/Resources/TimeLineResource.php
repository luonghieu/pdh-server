<?php

namespace App\Http\Resources;

use App\Traits\ResourceResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeLineResource extends JsonResource
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
            'user' => new UserResource($this->user),
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image,
            'location' => $this->location,
            'total_favorites' => $this->favorites_count,
            'hidden' => $this->hidden,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
