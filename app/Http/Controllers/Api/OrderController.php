<?php

namespace App\Http\Controllers\Api;

use App\Cast;
use App\CastClass;
use App\Enums\CastOrderStatus;
use App\Enums\CastOrderType;
use App\Enums\OfferStatus;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\RoomType;
use App\Http\Resources\OrderResource;
use App\Notifications\AcceptedOffer;
use App\Notifications\CallOrdersCreated;
use App\Notifications\CreateNominatedOrdersForCast;
use App\Notifications\CreateNominationOrdersForCast;
use App\Offer;
use App\Order;
use App\Room;
use App\Services\LogService;
use App\Tag;
use App\Traits\DirectRoom;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class OrderController extends ApiController
{
    use DirectRoom;

    public function create(Request $request)
    {
        $user = $this->guard()->user();
        $rules = [
            'prefecture_id' => 'nullable|exists:prefectures,id',
            'address' => 'required',
            'date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'duration' => 'required|numeric|min:1|max:10',
            'total_cast' => 'required|numeric|min:1',
            'temp_point' => 'required',
            'class_id' => 'required|exists:cast_classes,id',
            'type' => 'required|in:1,2,3,4',
            'tags' => '',
            'nominee_ids' => '',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        if (!$user->status) {
            return $this->respondErrorMessage(trans('messages.freezing_account'), 403);
        }

        $input = $request->only([
            'prefecture_id',
            'address',
            'date',
            'start_time',
            'duration',
            'total_cast',
            'temp_point',
            'class_id',
            'type',
        ]);

        $start_time = Carbon::parse($request->date . ' ' . $request->start_time);
        $end_time = $start_time->copy()->addHours($input['duration']);

        if (now()->second(0)->diffInMinutes($start_time, false) < 59) {
            return $this->respondErrorMessage(trans('messages.time_invalid'), 400);
        }

        /* if (!$user->cards->first()) {
        return $this->respondErrorMessage(trans('messages.card_not_exist'), 404);
        }

        $maxTime = $end_time->copy()->addHours(10);
        if ($maxTime->month > $user->card->exp_month && $maxTime->year == $user->card->exp_year || $maxTime->year > $user->card->exp_year) {
        return $this->respondErrorMessage(trans('messages.card_expired'), 406);
        } */

        if (!$request->nominee_ids) {
            $input['type'] = OrderType::CALL;
        } else {
            $listNomineeIds = explode(",", trim($request->nominee_ids, ","));
            $counter = Cast::whereIn('id', $listNomineeIds)->count();

            if ($request->total_cast != $counter) {
                $input['type'] = OrderType::HYBRID;
            }
        }

        $input['end_time'] = $end_time->format('H:i');

        if (!$request->prefecture_id) {
            $input['prefecture_id'] = 13;
        }

        $input['status'] = OrderStatus::OPEN;

        try {
            $when = Carbon::now()->addSeconds(3);
            DB::beginTransaction();
            $order = $user->orders()->create($input);

            if ($request->tags) {
                $listTags = explode(",", trim($request->tags, ","));
                $tagIds = Tag::whereIn('name', $listTags)->pluck('id');
                $order->tags()->attach($tagIds);
            }

            if (OrderType::CALL != $input['type']) {
                if (count($listNomineeIds) != $counter) {
                    return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
                }

                $order->nominees()->attach($listNomineeIds, [
                    'type' => CastOrderType::NOMINEE,
                    'status' => CastOrderStatus::OPEN,
                ]);

                if (1 == $request->total_cast && 1 == $counter) {
                    $ownerId = $order->user_id;
                    $nominee = $order->nominees()->first();
                    $room = $this->createDirectRoom($ownerId, $nominee->id);

                    $order->room_id = $room->id;
                    $order->save();

                    if (OrderType::NOMINATION == $order->type) {
                        $order->nominees()->updateExistingPivot(
                            $nominee->id,
                            [
                                'cost' => $nominee->cost,
                            ],
                            false
                        );

                        $nominee->notify(
                            (new CreateNominationOrdersForCast($order->id))->delay(now()->addSeconds(3))
                        );
                    }
                }

                if (OrderType::NOMINATED_CALL == $order->type || OrderType::HYBRID == $order->type) {
                    $nominees = $order->nominees;

                    \Notification::send(
                        $nominees,
                        (new CreateNominatedOrdersForCast($order->id))->delay(now()->addSeconds(3))
                    );
                }
            } else {
                $casts = Cast::where('class_id', $request->class_id)->get();

                \Notification::send(
                    $casts,
                    (new CallOrdersCreated($order->id))->delay(now()->addSeconds(3))
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }

        return $this->respondWithData(OrderResource::make($order));
    }

    public function show($id)
    {
        $order = Order::with('tags', 'user', 'casts')->find($id);

        if (!$order) {
            return $this->respondErrorMessage(trans('messages.order_not_found'), 404);
        }

        return $this->respondWithData(new OrderResource($order));
    }

    public function price(Request $request, $offer = null)
    {
        if (isset($request->offer)) {
            $offer = $request->offer;
        }

        $rules = [
            'date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'duration' => 'numeric|min:1|max:10',
            'class_id' => 'exists:cast_classes,id',
            'type' => 'required|in:1,2,3,4',
            'nominee_ids' => '',
            'total_cast' => 'required|numeric|min:1',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $orderStartTime = Carbon::parse($request->date . ' ' . $request->start_time);
        $stoppedAt = $orderStartTime->copy()->addHours($request->duration);

        //nightTime

        $nightTime = 0;
        $allowanceStartTime = Carbon::parse('00:01:00');
        $allowanceEndTime = Carbon::parse('04:00:00');

        $startDay = Carbon::parse($orderStartTime)->startOfDay();
        $endDay = Carbon::parse($stoppedAt)->startOfDay();

        $timeStart = Carbon::parse(Carbon::parse($orderStartTime->format('H:i:s')));
        $timeEnd = Carbon::parse(Carbon::parse($stoppedAt->format('H:i:s')));

        $allowance = false;

        if ($startDay->diffInDays($endDay) != 0 && $stoppedAt->diffInMinutes($endDay) != 0) {
            $allowance = true;
        }

        if ($timeStart->between($allowanceStartTime, $allowanceEndTime) || $timeEnd->between($allowanceStartTime, $allowanceEndTime)) {
            $allowance = true;
        }

        if ($timeStart < $allowanceStartTime && $timeEnd > $allowanceEndTime) {
            $allowance = true;
        }

        if ($allowance) {
            $nightTime = $stoppedAt->diffInMinutes($endDay);
        }

        //allowance

        $totalCast = $request->total_cast;
        $allowancePoint = 0;
        if ($nightTime) {
            $allowancePoint = $totalCast * 4000;
        }

        //orderPoint

        $orderPoint = 0;
        $orderDuration = $request->duration * 60;
        $nomineeIds = explode(",", trim($request->nominee_ids, ","));

        if (OrderType::NOMINATION != $request->type) {
            $cost = CastClass::find($request->class_id)->cost;
            $orderPoint = $totalCast * (($cost / 2) * floor($orderDuration / 15));
        } else {
            $cost = Cast::find($nomineeIds[0])->cost;
            $orderPoint = ($cost / 2) * floor($orderDuration / 15);
        }

        //ordersFee

        $orderFee = 0;
        if (OrderType::NOMINATION != $request->type) {
            if (!isset($offer)) {
                if (!empty($nomineeIds[0])) {
                    $multiplier = floor($orderDuration / 15);
                    $orderFee = 500 * $multiplier * count($nomineeIds);
                }
            }
        }

        return $this->respondWithData($orderPoint + $orderFee + $allowancePoint);
    }

    public function getDayOfMonth(Request $request)
    {
        $month = $request->month;
        $data['month'] = $month;

        return getDay($data);
    }

    public function createOrderOffer(Request $request)
    {
        $user = $this->guard()->user();
        $input = $request->only([
            'prefecture_id',
            'address',
            'date',
            'start_time',
            'duration',
            'total_cast',
            'temp_point',
            'class_id',
            'type',
            'offer_id',
        ]);

        $offer = Offer::find($request->offer_id);

        if (!$offer || OfferStatus::ACTIVE != $offer->status) {
            return $this->respondErrorMessage(trans('messages.order_timeout'), 422);
        }

        /* if (!$user->cards->first()) {
        return $this->respondErrorMessage(trans('messages.card_not_exist'), 409);
        } */

        $now = Carbon::now()->second(0);

        $start_time = Carbon::parse($request->date . ' ' . $request->start_time);
        $end_time = $start_time->copy()->addHours($input['duration']);

        /* $maxTime = $end_time->copy()->addHours(10);
        if ($maxTime->month > $user->card->exp_month && $maxTime->year == $user->card->exp_year || $maxTime->year > $user->card->exp_year) {
        return $this->respondErrorMessage(trans('messages.card_expired'), 406);
        } */

        if ($now->second(0)->diffInMinutes($start_time, false) < 59) {
            return $this->respondErrorMessage(trans('messages.time_invalid'), 400);
        }

        $startHourFrom = (int) Carbon::parse($offer->start_time_from)->format('H');
        $startHourTo = (int) Carbon::parse($offer->start_time_to)->format('H');

        $startTimeTo = Carbon::createFromFormat('Y-m-d H:i:s', $offer->date . ' ' . $offer->start_time_to);
        if ($startHourTo < $startHourFrom) {
            $startTimeTo = $startTimeTo->copy()->addDay();
        }

        $input['end_time'] = $end_time->format('H:i');

        $castIds = explode(",", trim($request->nominee_ids, ","));
        $input['end_time'] = $end_time->format('H:i');
        $input['status'] = OrderStatus::ACTIVE;

        try {
            DB::beginTransaction();
            $order = $user->orders()->create($input);

            $order->nominees()->attach($castIds, [
                'type' => CastOrderType::CANDIDATE,
                'status' => CastOrderStatus::ACCEPTED,
                'accepted_at' => Carbon::now(),
            ]);

            $orderStartTime = Carbon::parse($order->date . ' ' . $order->start_time);
            $orderEndTime = $orderStartTime->copy()->addMinutes($order->duration * 60);
            $nightTime = $order->nightTime($orderEndTime);
            $allowance = $order->allowance($nightTime);

            foreach ($castIds as $castId) {
                $orderPoint = $order->orderPoint();
                $order->nominees()->updateExistingPivot(
                    $castId,
                    [
                        'temp_point' => $orderPoint + $allowance,
                    ],
                    false
                );
            }

            if (1 == count($castIds)) {
                $room = $this->createDirectRoom($user->id, $castIds[0]);
            } else {
                $room = new Room;
                $room->order_id = $order->id;
                $room->owner_id = $order->user_id;
                $room->type = RoomType::GROUP;
                $room->save();

                $casts = $order->casts()->get();

                $data = [$order->user_id];
                foreach ($casts as $cast) {
                    $data = array_merge($data, [$cast->pivot->user_id]);
                }

                $room->users()->attach($data);
            }

            $order->room_id = $room->id;
            $order->update();

            $offer->status = OfferStatus::DONE;
            $offer->update();
            $delay = Carbon::now()->addSeconds(3);
            $order->user->notify((new AcceptedOffer($order->id))->delay($delay));
            DB::commit();

            return $this->respondWithData(new OrderResource($order));
        } catch (\Exception $e) {
            DB::rollBack();
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }
}
