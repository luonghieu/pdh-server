<?php

namespace App\Http\Controllers\Api;

use App\Cast;
use App\Enums\CastOrderType;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\RoomType;
use App\Http\Resources\OrderResource;
use App\Order;
use App\Room;
use App\Services\LogService;
use App\Tag;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class OrderController extends ApiController
{
    public function create(Request $request)
    {
        $user = $this->guard()->user();

        $rules = [
            'prefecture_id' => 'nullable|exists:prefectures,id',
            'address' => 'required',
            'date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'duration' => 'required|numeric|min:1|max:10',
            'total_cast' => 'required|min:1',
            'temp_point' => 'required',
            'class_id' => 'required|exists:cast_classes,id',
            'type' => 'required|in:1,2,3',
            'tags' => '',
            'nominee_ids' => '',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
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

        $start_time = Carbon::parse($request->start_time);
        $end_time = Carbon::parse($input['start_time'])->addHours($input['duration']);

        $orders = $user->orders()
            ->where('date', $request->date)
            ->whereIn('status', [OrderStatus::OPEN, OrderStatus::ACTIVE, OrderStatus::PROCESSING])
            ->where(function ($query) use ($start_time, $end_time) {
                $query->orWhereBetween('start_time', [$start_time, $end_time]);
                $query->orWhereBetween('end_time', [$start_time, $end_time]);
                $query->orWhere([
                    ['start_time', '<', $start_time],
                    ['end_time', '>', $start_time],
                ]);
            });

        if ($orders->count() > 0) {
            return $this->respondErrorMessage(trans('messages.order_same_time'), 409);
        }

        // if (!$user->cards->first()) {
        //     return $this->respondErrorMessage(trans('messages.card_not_exist'), 404);
        // }

        $input['end_time'] = $end_time->format('H:i');

        if (!$request->prefecture_id) {
            $input['prefecture_id'] = 13;
        }

        if (!$request->nominee_ids) {
            $input['type'] = OrderType::CALL;
        } else {
            $listNomineeIds = explode(",", trim($request->nominee_ids, ","));
            $counter = Cast::whereIn('id', $listNomineeIds)->count();

            if (1 == $counter) {
                $input['type'] = OrderType::NOMINATION;
            }
        }

        $input['status'] = OrderStatus::OPEN;

        try {
            DB::beginTransaction();
            $order = $user->orders()->create($input);

            if ($request->tags) {
                $listTags = explode(",", trim($request->tags, ","));
                $tagIds = Tag::whereIn('name', $listTags)->pluck('id');
                $order->tags()->attach($tagIds);
            }

            if (OrderType::CALL != $input['type']) {
                if (count($listNomineeIds) != $counter) {
                    return $this->respondErrorMessage(trans('messages.action_not_performed'), 402);
                }

                $order->nominees()->attach($listNomineeIds, ['type' => CastOrderType::NOMINEE]);

                if (OrderType::NOMINATION == $order->type) {
                    $nominee = $order->nominees()->first()->id;
                    $isRoomExists = Room::active()->where('type', RoomType::DIRECT)
                        ->whereHas('users', function ($query) use ($nominee) {
                            $query->where('user_id', $nominee);
                        })
                        ->whereHas('users', function ($query) use ($order) {
                            $query->where('user_id', $order->user_id);
                        })
                        ->count();

                    if (!$isRoomExists) {
                        $room = new Room;
                        $room->owner_id = $order->user_id;
                        $room->type = RoomType::DIRECT;
                        $room->save();

                        $room->users()->attach([$nominee, $order->user_id]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
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
}
