<?php

namespace App\Http\Controllers\Api;

use App\BodyType;
use App\Enums\CohabitantType;
use App\Enums\DrinkVolumeType;
use App\Enums\SiblingsType;
use App\Enums\SmokingType;
use App\Enums\UserGender;
use App\Enums\UserType;
use App\Job;
use App\Prefecture;
use App\Salary;
use Webpatser\Uuid\Uuid;

class GlossaryController extends ApiController
{
    public function glossary()
    {
        $count = 1500;

        for ($i= 0; $i < $count; $i++) {
            $data = [
                'email' => null,
                'fullname' => 'fullname_' . \Webpatser\Uuid\Uuid::generate()->time,
                'nickname' => 'nickname_' . \Webpatser\Uuid\Uuid::generate()->time,
                'line_user_id' => null,
                'type' => \App\Enums\UserType::GUEST,
                'status' => \App\Enums\Status::ACTIVE,
                'provider' => \App\Enums\ProviderType::LINE,
                'device_type' => \App\Enums\DeviceType::WEB,
                'is_verified' => 1
            ];

            $user = \App\User::create($data);
            $user->avatars()->create([
                'path' => 'https://bucket-cheers-stg.s3.us-west-2.amazonaws.com/a90fa7c0-ee4a-11e8-bffa-7359cb0b2e10.',
                'thumbnail' => 'https://bucket-cheers-stg.s3.us-west-2.amazonaws.com/a90fa7c0-ee4a-11e8-bffa-7359cb0b2e10.',
                'is_default' => true,
            ]);

            $room = \App\Room::create([
                'owner_id' => $user->id
            ]);
            $room->users()->attach([1, $user->id]);
        }

        dd(123123123123);

        $drinkVolumes = [];
        $smokings = [];
        $siblings = [];
        $cohabitants = [];
        $genders = [];

        foreach (DrinkVolumeType::toArray() as $value) {
            $drinkVolumes[] = ['id' => $value, 'name' => DrinkVolumeType::getDescription($value)];
        }

        $data['drink_volumes'] = $drinkVolumes;

        foreach (SmokingType::toArray() as $value) {
            $smokings[] = ['id' => $value, 'name' => SmokingType::getDescription($value)];
        }

        $data['smokings'] = $smokings;

        foreach (SiblingsType::toArray() as $value) {
            $siblings[] = ['id' => $value, 'name' => SiblingsType::getDescription($value)];
        }

        $data['siblings'] = $siblings;

        foreach (CohabitantType::toArray() as $value) {
            $cohabitants[] = ['id' => $value, 'name' => CohabitantType::getDescription($value)];
        }

        $data['cohabitants'] = $cohabitants;

        foreach (UserGender::toArray() as $value) {
            $genders[] = ['id' => $value, 'name' => UserGender::getDescription($value)];
        }

        $data['genders'] = $genders;

        $data['prefectures'] = Prefecture::where('id', '<=', 47)->get(['id', 'name'])->toArray();

        $hometowns = Prefecture::all(['id', 'name']);
        $data['hometowns'] = $hometowns->prepend($hometowns->pull(48))->toArray();

        $data['body_types'] = BodyType::all(['id', 'name'])->toArray();

        $data['salaries'] = Salary::all(['id', 'name'])->toArray();

        $data['jobs'] = Job::all(['id', 'name'])->toArray();

        $data['order_options'] = config('common.order_options');

        return $this->respondWithData($data);
    }
}
