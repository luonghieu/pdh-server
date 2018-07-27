<?php

namespace App\Http\Controllers\Api;

use App\BodyType;
use App\Cast;
use App\Enums\CastOrderStatus;
use App\Enums\CohabitantType;
use App\Enums\DrinkVolumeType;
use App\Enums\SiblingsType;
use App\Enums\SmokingType;
use App\Enums\UserGender;
use App\Job;
use App\Order;
use App\Prefecture;
use App\Salary;
use Carbon\Carbon;

class GlossaryController extends ApiController
{
    public function glossary()
    {
        $order = Order::find(7);
        return $this->caculatePoint($order, 3);
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

        return $this->respondWithData($data);
    }

    private function caculatePoint($order, $castId) {
        $orderHasAllowance = false;
        $nightAllowance = 0;

        $cast = $order->belongsToMany(Cast::class)->where('cast_order.status', CastOrderStatus::PROCESSING)
            ->withTimestamps()->withPivot('started_at', 'stopped_at')->where('user_id', $castId)->first();

        $startDate = Carbon::parse($cast->pivot->started_at);
        $endDate = Carbon::parse($cast->pivot->stopped_at);

        $orderHasAllowance = $this->isAllowanceOrder($startDate, $endDate);


        dump($orderHasAllowance);
        dd('------------------------');
    }

    private function isAllowanceOrder($startDate, $endDate)
    {
        $allowanceStartTime = Carbon::parse('00:00:00');
        $allowanceEndTime = Carbon::parse('04:00:00');

        $startDay = Carbon::parse($startDate)->startOfDay();
        $endDay = Carbon::parse($endDate)->startOfDay();

        $timeStart = Carbon::parse(Carbon::parse($startDate->format('H:i:s')));
        $timeEnd = Carbon::parse(Carbon::parse($endDate->format('H:i:s')));

        if ($startDay->diffInDays($endDay) != 0) {
            $orderHasAllowance = true;
        } elseif ($timeStart->between($allowanceStartTime, $allowanceEndTime, true)) {
            $orderHasAllowance = true;
        } elseif ($timeEnd->between($allowanceStartTime, $allowanceEndTime, true)) {
            $orderHasAllowance = true;
        } elseif ($startDay->diffInDays($endDay) != 0 && $timeEnd > $allowanceEndTime) {
            $orderHasAllowance = true;
        } else {
            $orderHasAllowance = false;
        }


        return $orderHasAllowance;
    }
}
