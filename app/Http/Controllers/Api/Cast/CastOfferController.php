<?php

namespace App\Http\Controllers\Api\Cast;

use App\Cast;
use App\Enums\UserType;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\CastOfferResource;
use App\Notifications\CastCreateOffer;
use App\Services\LogService;
use App\User;
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
            $offer = $cast->offers()->create([
                'date' => $request->date,
                'start_time' => $request->start_time,
                'duration' => $request->duration,
                'cast_class_id' => $cast->class_id,
                'address' => $request->address,
                'guest_id' => $request->user_id,
            ]);

            $guest->notify(new CastCreateOffer($offer->id));
            return $this->respondWithData(CastOfferResource::make($offer));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }
}
