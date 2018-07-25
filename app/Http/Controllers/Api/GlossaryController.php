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
        $private = [
            'id' => 0,
            'name' => '非公開',
        ];

        $data['drink_volumes'] = [
            [
                'id' => 1,
                'name' => DrinkVolumeType::getDescription(DrinkVolumeType::YES),
            ],
            [
                'id' => 2,
                'name' => DrinkVolumeType::getDescription(DrinkVolumeType::OCCASIONALLY),
            ],
            [
                'id' => 3,
                'name' => DrinkVolumeType::getDescription(DrinkVolumeType::NO),
            ],
        ];

        $data['smokings'] = [
            [
                'id' => 1,
                'name' => SmokingType::getDescription(SmokingType::YES),
            ],
            [
                'id' => 2,
                'name' => SmokingType::getDescription(SmokingType::OPTIONAL),
            ],
            [
                'id' => 3,
                'name' => SmokingType::getDescription(SmokingType::NO),
            ],
        ];

        $data['siblings'] = [
            [
                'id' => 1,
                'name' => SiblingsType::getDescription(SiblingsType::ELDEST),
            ],
            [
                'id' => 2,
                'name' => SiblingsType::getDescription(SiblingsType::SECOND),
            ],
            [
                'id' => 3,
                'name' => SiblingsType::getDescription(SiblingsType::OTHER),
            ],
        ];

        $data['cohabitants'] = [

            [
                'id' => 1,
                'name' => CohabitantType::getDescription(CohabitantType::ALONE),
            ],
            [
                'id' => 2,
                'name' => CohabitantType::getDescription(CohabitantType::FAMILY),
            ],
            [
                'id' => 3,
                'name' => CohabitantType::getDescription(CohabitantType::SHARE_HOUSE),
            ],
            [
                'id' => 4,
                'name' => CohabitantType::getDescription(CohabitantType::OTHER),
            ],
        ];

        $data['genders'] = [
            $private,
            [
                'id' => 1,
                'name' => UserGender::getDescription(UserGender::MALE),
            ],
            [
                'id' => 2,
                'name' => UserGender::getDescription(UserGender::FEMALE),
            ],
        ];

        $data['prefectures'] = Prefecture::where('id', '<=', 47)->get(['id', 'name'])->toArray();

        $hometowns = Prefecture::all(['id', 'name']);
        $data['hometowns'] = $hometowns->prepend($hometowns->pull(48))->toArray();

        $data['body_types'] = BodyType::all(['id', 'name'])->toArray();

        $data['salaries'] = Salary::all(['id', 'name'])->toArray();

        $data['jobs'] = Job::all(['id', 'name'])->toArray();

        return $this->respondWithData($data);
    }
}
