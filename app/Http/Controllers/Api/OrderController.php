<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Http\Resources\OrderResource;
use App\Order;
use App\Services\LogService;
use App\Tag;
use Illuminate\Http\Request;

class OrderController extends ApiController
{
    public function create(Request $request)
    {
        $user = $this->guard()->user();

        $rules = [
            'prefecture_id' => 'nullable|exists:prefectures,id',
            'address' => 'required',
            'date' => 'required|date|after_or_equal:today',
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

        if (null == $input['prefecture_id']) {
            $input['prefecture_id'] == 13;
        }

        if ($request->tags) {
            $listTags = explode(",", trim($request->tags, ","));
            $tagIds = Tag::whereIn('name', $listTags)->pluck('id');
        }

        $input['end_time'] = \Carbon\Carbon::parse($input['start_time'])->addHours($input['duration'])->format('H:i');

        $input['status'] = OrderStatus::OPEN;

        try {
            $order = $user->orders()->create($input);

            $order->tags()->attach($tagIds);

            if ((OrderType::NOMINATED_CALL == $request->type) && $request->nominee_ids) {
                $listNomineeIds = explode(",", trim($request->nominee_ids, ","));

                $order->nominees()->attach($listNomineeIds);
            }
        } catch (\Exception $e) {
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
