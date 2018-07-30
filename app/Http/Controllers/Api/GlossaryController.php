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
        $nightAllowance = 0;
        $extralTime = 0;

        $cast = $order->belongsToMany(Cast::class)->where('cast_order.status', CastOrderStatus::PROCESSING)
            ->withTimestamps()->withPivot('started_at', 'stopped_at')->where('user_id', $castId)->first();

        $startDate = Carbon::parse($order->date . ' ' .$order->start_time);
        $endDate = $startDate->copy()->addHours($order->duration);

        $orderHasAllowance = $this->isAllowanceOrder($startDate, $endDate);
        if ($orderHasAllowance) {
            $nightAllowance = 4000;
        }

        $castStoppedAt = Carbon::parse($cast->pivot->stopped_at);
        if ($castStoppedAt > $endDate) {
            $extralTime = $castStoppedAt->diffInMinutes($endDate);
        }

        $extraCount = 0;
        if ($extralTime > 15) {
            dump($extralTime);
            while ($extralTime >= 16) {
                $extraCount++;
                $extralTime = $extralTime - 16;
                dump($extralTime);
            }
        }

        dd($extraCount);
        dd('------------------------');
    }

    private function isAllowanceOrder($startDate, $endDate)
    {
        $allowanceStartTime = Carbon::parse('00:01:00');
        $allowanceEndTime = Carbon::parse('04:00:00');

        $startDay = Carbon::parse($startDate)->startOfDay();
        $endDay = Carbon::parse($endDate)->startOfDay();

        $timeStart = Carbon::parse(Carbon::parse($startDate->format('H:i:s')));
        $timeEnd = Carbon::parse(Carbon::parse($endDate->format('H:i:s')));

        if ($startDay->diffInDays($endDay) != 0 && !$endDate->eq($endDate->copy()->startOfDay())) {
            return true;
        }

        if ($timeStart->between($allowanceStartTime, $allowanceEndTime) || $timeEnd->between($allowanceStartTime, $allowanceEndTime)) {
            return true;
        }

        if ($timeStart < $allowanceStartTime  && $timeEnd > $allowanceEndTime) {
            return true;
        }

        return false;
    }

    private function hasExtraTime($castOrders) {

    }
}
