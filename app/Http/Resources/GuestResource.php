<?php

namespace App\Http\Resources;

use App\Job;
use App\Room;
use App\Salary;
use App\BodyType;
use App\Enums\RoomType;
use App\Enums\SmokingType;
use App\Enums\SiblingsType;
use App\Enums\CohabitantType;
use App\Enums\DrinkVolumeType;
use App\Traits\ResourceResponse;
use App\Repositories\JobRepository;
use App\Repositories\SalaryRepository;
use App\Repositories\BodyTypeRepository;
use App\Repositories\PrefectureRepository;
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
            'salary' => $this->salary_id ? app(SalaryRepository::class)->find($this->salary_id)->name : '',
            'body_type_id' => $this->body_type_id,
            'body_type' => $this->body_type_id ? app(BodyTypeRepository::class)->find($this->body_type_id)->name : '',
            'prefecture_id' => $this->prefecture_id,
            'prefecture' => $this->prefecture_id ? app(PrefectureRepository::class)->find($this->prefecture_id)->name : '',
            'hometown_id' => $this->hometown_id,
            'hometown' => $this->hometown_id ? app(PrefectureRepository::class)->find($this->hometown_id)->name : '',
            'job_id' => $this->job_id,
            'job' => $this->job_id ? app(JobRepository::class)->find($this->job_id)->name : '',
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
            'room_id' => $this->room_id,
            'card' => CardResource::make($this->whenLoaded('card')),
            'line_qr' => $this->line_qr,
            'post_code' => $this->post_code,
            'address' => $this->address,
            'fullname_kana' => $this->fullname_kana,
        ]);
    }
}
