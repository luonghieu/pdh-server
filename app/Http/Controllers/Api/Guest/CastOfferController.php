<?php

namespace App\Http\Controllers\Api\Guest;

use App\CastOffer;
use App\Enums\CastOfferStatus;
use App\Enums\OrderType;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\CastOfferResource;
use App\Notifications\GuestCancelOrderOfferFromCast;
use App\Services\LogService;
use Carbon\Carbon;

class CastOfferController extends ApiController
{
    public function show($id)
    {
        $offer = CastOffer::where('status', CastOfferStatus::PENDING)->find($id);

        if (!$offer) {
            return $this->respondErrorMessage(trans('messages.offer_not_found'), 404);
        }

        return $this->respondWithData(new CastOfferResource($offer));
    }

    public function cancel($id)
    {
        $offer = CastOffer::where('status', CastOfferStatus::PENDING)->find($id);

        if (!$offer) {
            return $this->respondErrorMessage(trans('messages.offer_not_found'), 404);
        }

        try {
            $offer->status = CastOfferStatus::DENIED;
            $offer->save();

            \Notification::send($offer->cast, new GuestCancelOrderOfferFromCast($offer));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
        }

        return $this->respondWithData(new CastOfferResource($offer));
    }

    public function create($id)
    {
        $user = $this->guard()->user();
        $offer = CastOffer::where('status', CastOfferStatus::PENDING)->find($id);

        if (!$user->status) {
            return $this->respondErrorMessage(trans('messages.freezing_account'), 403);
        }

        if (!$offer) {
            return $this->respondErrorMessage(trans('messages.offer_not_found'), 404);
        }

        $input = [
            'date' => Carbon::parse($offer->date)->format('Y-m-d'),
            'start_time' => Carbon::parse($offer->start_time)->format('H:i'),
            'prefecture_id' => $offer->prefecture_id,
            'address' => $offer->address,
            'class_id' => $offer->cast_class_id,
            'type' => OrderType::NOMINATION,
            'duration' => (int) $offer->duration,
            'total_cast' => 1,
            'temp_point' => 'required',
        ];

        $start_time = Carbon::parse($offer->date . ' ' . $offer->start_time);
        $end_time = $start_time->copy()->addHours((int) $offer->duration);

        if (now()->second(0)->diffInMinutes($start_time, false) < 29) {
            return $this->respondErrorMessage(trans('messages.time_invalid'), 400);
        }

        if (!$request->payment_method || OrderPaymentMethod::DIRECT_PAYMENT != $request->payment_method) {
            if (!$user->is_card_registered) {
                return $this->respondErrorMessage(trans('messages.card_not_exist'), 404);
            }
        }

        $input['end_time'] = $end_time->format('H:i');

        dd($input);
    }
}
