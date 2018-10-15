<?php

namespace App\Http\Controllers\Api;

use App\BodyType;
use App\Enums\CohabitantType;
use App\Enums\DrinkVolumeType;
use App\Enums\SiblingsType;
use App\Enums\SmokingType;
use App\Enums\UserGender;
use App\Job;
use App\Prefecture;
use App\Salary;

class GlossaryController extends ApiController
{
    public function glossary()
    {
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
