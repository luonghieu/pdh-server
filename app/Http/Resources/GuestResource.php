<?php

namespace App\Http\Resources;

use App\BodyType;
use App\Enums\CohabitantType;
use App\Enums\DrinkVolumeType;
use App\Enums\SiblingsType;
use App\Enums\SmokingType;
use App\Http\Resources\AvatarResource;
use App\Job;
use App\Repositories\PrefectureRepository;
use App\Salary;
use App\Traits\ResourceResponse;
use App\User;
use Illuminate\Http\Resources\Json\Resource;

class GuestResource extends Resource
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
            'facebook_id' => $this->facebook_id,
            'line_id' => $this->line_id,
            'email' => $this->email,
            'nickname' => $this->nickname,
            'fullname' => $this->fullname,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'age' => $this->age,
            'height' => $this->height,
            'salary_id' => $this->salary_id,
            'salary' => $this->salary_id ? Salary::find($this->salary_id)->name : '',
            'body_type_id' => $this->body_type_id,
            'body_type' => $this->body_type_id ? BodyType::find($this->body_type_id)->name : '',
            'prefecture_id' => $this->prefecture_id,
            'prefecture' => $this->prefecture_id ? app(PrefectureRepository::class)->find($this->prefecture_id)->name : '',
            'hometown_id' => $this->hometown_id,
            'hometown' => $this->hometown_id ? app(PrefectureRepository::class)->find($this->hometown_id)->name : '',
            'job_id' => $this->job_id,
            'job' => $this->job_id ? Job::find($this->job_id)->name : '',
            'drink_volume_type' => $this->drink_volume_type,
            'drink_volume' => $this->drink_volume_type ? DrinkVolumeType::getDescription($this->drink_volume_type) : '',
            'smoking_type' => $this->smoking_type,
            'smoking' => $this->smoking_type ? SmokingType::getDescription($this->smoking_type) : '',
            'siblings_type' => $this->siblings_type,
            'siblings' => $this->siblings_type ? SiblingsType::getDescription($this->siblings_type) : '',
            'cohabitant_type' => $this->cohabitant_type,
            'cohabitant' => $this->cohabitant_type ? CohabitantType::getDescription($this->cohabitant_type) : '',
            'intro' => $this->intro,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'point' => $this->point,
            'is_favorited' => $this->is_favorited,
            'is_blocked' => $this->is_blocked,
            'avatars' => AvatarResource::collection($this->avatars),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'last_active_at' => $this->last_active_at,
            'last_active' => $this->last_active,
            'is_online' => $this->is_online,
            'rating_score' => $this->rating_score,
        ]);
    }
}
