<?php

namespace App\Http\Controllers\Api;

use App\Cast;
use App\Enums\CastOrderStatus;
use App\Enums\CastOrderType;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Http\Resources\OrderResource;
use App\Notifications\CreateNominationOrdersForCast;
use App\Order;
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

        if (now()->diffInMinutes($start_time, false) < 19) {
            return $this->respondErrorMessage(trans('messages.time_invalid'), 400);
        }

        $orders = $user->orders()
            ->whereIn('status', [OrderStatus::OPEN, OrderStatus::ACTIVE, OrderStatus::PROCESSING])
            ->where(function ($query) use ($start_time, $end_time) {
                $query->orWhereRaw("concat_ws(' ',`date`,`start_time`) >= '$start_time' and concat_ws(' ',`date`,`start_time`) <= '$end_time'"
                );

                $query->orWhereRaw("DATE_ADD(concat_ws(' ',`date`,`start_time`), INTERVAL `duration` HOUR) >= '$start_time' and DATE_ADD(concat_ws(' ',`date`,`start_time`), INTERVAL `duration` HOUR) <= '$end_time'"
                );
                $query->orWhereRaw("concat_ws(' ',`date`,`start_time`) <= '$start_time' and DATE_ADD(concat_ws(' ',`date`,`start_time`), INTERVAL `duration` HOUR) >= '$end_time'"
                );
            });
        $listCastMatching = 0;

        if (!$request->nominee_ids) {
            $input['type'] = OrderType::CALL;
        } else {
            $listNomineeIds = explode(",", trim($request->nominee_ids, ","));
            $counter = Cast::whereIn('id', $listNomineeIds)->count();

            $listCastMatching = $user->orders()->whereHas('casts', function ($q) use ($listNomineeIds) {
                $q->whereIn('users.id', $listNomineeIds);
            })
                ->whereIn('status', [OrderStatus::ACTIVE, OrderStatus::PROCESSING])
                ->count();

            if ($request->total_cast != $counter) {
                $input['type'] = OrderType::HYBRID;
            }
        }

        if ($orders->count() > 0 || $listCastMatching > 0) {
            return $this->respondErrorMessage(trans('messages.order_same_time'), 409);
        }

        if (!$user->cards->first()) {
            return $this->respondErrorMessage(trans('messages.card_not_exist'), 404);
        }

        $input['end_time'] = $end_time->format('H:i');

        if (!$request->prefecture_id) {
            $input['prefecture_id'] = 13;
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
                    return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
                }

                $order->nominees()->attach($listNomineeIds, [
                    'type' => CastOrderType::NOMINEE,
                    'status' => CastOrderStatus::OPEN,
                ]);


                if (1 == $request->total_cast &&  1 == $counter) {
                    $ownerId = $order->user_id;
                    $nominee = $order->nominees()->first();
                    $room = $this->createDirectRoom($ownerId, $nominee->id);

                    $order->room_id = $room->id;
                    $order->save();

                    if ($order->type == OrderType::NOMINATION) {
                        $nominee->notify(new CreateNominationOrdersForCast($order));
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
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
