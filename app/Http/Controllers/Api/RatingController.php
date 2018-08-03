<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Order;
use App\Rating;
use App\Services\LogService;
use App\User;
use Illuminate\Http\Request;

class RatingController extends ApiController
{
    public function create(Request $request)
    {
        $rules = [
            'order_id' => 'required',
            'rated_id' => 'required',
            'satisfaction' => 'between:1,5|numeric|required_without:score',
            'appearance' => 'between:1,5|numeric|required_without:score',
            'friendliness' => 'between:1,5|numeric|required_without:score',
            'comment' => 'max:2000|required_without:score',
            'score' => 'between:1,5|numeric|',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $orderId = $request->order_id;

        $order = Order::find($orderId);

        if (!$order) {
            return $this->respondErrorMessage(trans('messages.order_not_found'), 404);
        }

        if (OrderStatus::DONE != $order->status) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        $user = $this->guard()->user();

        $castExists = $order->casts()->pluck('user_id')->toArray();

        if ($user->is_cast) {
            $isRated = Rating::where('user_id', $this->guard()->user()->id)->where('order_id', $orderId)->exists();

            if ($isRated) {
                return $this->respondErrorMessage(trans('messages.order_is_rated'), 409);
            }

            if (!$request->score || !$castExists || $request->rated_id != $order->user_id) {
                return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
            }
        } else {
            $ratedIds = $user->rates()->where('order_id', $orderId)->pluck('rated_id')->toArray();

            if (in_array($request->rated_id, $ratedIds) || $order->user_id != $user->id
                || !in_array($request->rated_id, $castExists)) {
                return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
            }
        }

        $rating = new Rating;
        $rating->user_id = $user->id;
        $rating->order_id = $orderId;
        $rating->rated_id = $request->rated_id;
        try {
            if ($user->is_cast) {
                $rating->score = $request->score;
                $rating->save();
            } else {
                $rating->satisfaction = $request->satisfaction;
                $rating->appearance = $request->appearance;
                $rating->friendliness = $request->friendliness;
                $rating->comment = $request->comment;
                $rating->score = round(($request->friendliness + $rating->appearance + $rating->satisfaction) / 3, 1);
                $rating->save();
            }
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
        return $this->respondWithNoData(trans('messages.rating_success'));
    }
}
