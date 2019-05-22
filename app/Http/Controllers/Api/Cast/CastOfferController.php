<?php

namespace App\Http\Controllers\Api\Cast;

use App\Cast;
use App\CastOffer;
use App\Enums\CastOfferStatus;
use App\Enums\UserType;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\CastOfferResource;
use App\Notifications\CastCreateOffer;
use App\Services\LogService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CastOfferController extends ApiController
{
    public function create(Request $request)
    {
        $rules = [
            'date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'duration' => 'required|numeric|min:1|max:10',
            'address' => 'required',
            'user_id' => 'required',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $guest = User::where('id', $request->user_id)->where('type', UserType::GUEST)->first();
        if (!$guest) {
            return $this->respondErrorMessage(trans('messages.user_not_found'), 404);
        }

        $user = $this->guard()->user();
        $cast = Cast::find($user->id);

        try {

            $orderStartTime = Carbon::parse($request->date . ' ' . $request->start_time);
            $orderEndTime = $orderStartTime->copy()->addMinutes($request->duration * 60);
            $nightTime = $this->nightTime($orderStartTime, $orderEndTime);
            $allowance = $this->allowance($nightTime);
            $orderPoint = $this->orderPoint($cast, $request->duration);

            $offer = $cast->offers()->create([
                'date' => $request->date,
                'start_time' => $request->start_time,
                'duration' => $request->duration,
                'cast_class_id' => $cast->class_id,
                'address' => $request->address,
                'guest_id' => $request->user_id,
                'temp_point' => $orderPoint + $allowance,
                'cost' => $cast->cost,
            ]);

            $guest->notify(new CastCreateOffer($offer->id));
            return $this->respondWithData(CastOfferResource::make($offer));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }

    private function nightTime($startedAt, $stoppedAt)
    {
        $nightTime = 0;
        $startDate = Carbon::parse($startedAt);
        $endDate = Carbon::parse($stoppedAt);

        $allowanceStartTime = Carbon::parse('00:01:00');
        $allowanceEndTime = Carbon::parse('04:00:00');

        $startDay = Carbon::parse($startDate)->startOfDay();
        $endDay = Carbon::parse($endDate)->startOfDay();

        $timeStart = Carbon::parse(Carbon::parse($startDate->format('H:i:s')));
        $timeEnd = Carbon::parse(Carbon::parse($endDate->format('H:i:s')));

        $allowance = false;

        if ($startDay->diffInDays($endDay) != 0 && $endDate->diffInMinutes($endDay) != 0) {
            $allowance = true;
        }

        if ($timeStart->between($allowanceStartTime, $allowanceEndTime) || $timeEnd->between($allowanceStartTime, $allowanceEndTime)) {
            $allowance = true;
        }

        if ($timeStart < $allowanceStartTime && $timeEnd > $allowanceEndTime) {
            $allowance = true;
        }

        if ($allowance) {
            $nightTime = $endDate->diffInMinutes($endDay);
        }

        return $nightTime;
    }

    private function allowance($nightTime)
    {
        if ($nightTime) {
            return 4000;
        }

        return 0;
    }

    private function orderPoint($cast, $orderDuration)
    {
        $cost = $cast->cost;
        $orderDuration = $orderDuration * 60;

        return ($cost / 2) * floor($orderDuration / 15);
    }

    public function cancel($id)
    {
        $user = $this->guard()->user();
        $offer = CastOffer::where('status', CastOfferStatus::PENDING)->where('user_id', $user->id)->find($id);

        if (!$offer) {
            return $this->respondErrorMessage(trans('messages.offer_not_found'), 404);
        }

        try {
            $offer->status = CastOfferStatus::CANCELED;
            $offer->save();

            return $this->respondWithData(CastOfferResource::make($offer));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }
}
