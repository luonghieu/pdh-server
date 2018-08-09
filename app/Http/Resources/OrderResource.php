<?php

namespace App\Http\Resources;

use App\CastClass;
use App\Enums\OrderType;
use App\Http\Resources\CastClassResource;
use App\Repositories\PrefectureRepository;
use App\Traits\ResourceResponse;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Carbon;

class OrderResource extends Resource
{
    use ResourceResponse;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        echo 'Order start date: ' . Carbon::parse($this->date . ' ' . $this->start_time)->format('Y/m/d H:i');
        echo('<br>');
        echo 'Order duration: ' . $this->duration;
        echo('<br>');
        echo 'Order type: ' . OrderType::getDescription($this->type) . " ($this->type)";
        echo('<br>');
        echo 'Order class: ' . $this->class_id;
        echo('<br>');
        echo 'Order cost: ' . $this->castClass->cost;
        echo('<br>');
        echo 'Total Cast: ' . $this->total_cast;

        return $this->filterNull([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'prefecture_id' => $this->prefecture_id,
            'prefecture' => $this->prefecture_id ? app(PrefectureRepository::class)->find($this->prefecture_id)->name : '',
            'address' => $this->address,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'duration' => $this->duration,
            'extra_time' => $this->extra_time,
            'night_time' => $this->night_time,
            'total_time' => $this->total_time,
            'total_cast' => $this->total_cast,
            'temp_point' => $this->temp_point,
            'total_point' => $this->total_point,
            'class_id' => $this->class_id,
            'cast_class' => CastClassResource::make($this->castClass),
            'type' => $this->type,
            'status' => $this->status,
            'actual_started_at' => $this->actual_started_at,
            'actual_ended_at' => $this->actual_ended_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'casts' => UserCollection::make($this->whenLoaded('casts')),
            'payment_requests' => PaymentRequestResource::collection($this->whenLoaded('paymentRequests')),
            'nominees' => UserCollection::make($this->whenLoaded('nominees')),
            'user' => new UserResource($this->user),
            'is_nominated' => $this->isNominated(),
            'user_status' => $this->user_status,
            // 'is_payment_requested' => $this->isPaymentRequested(),
            'room_id' => $this->room_id,
            'payment_status' => $this->payment_status,
            'cancel_fee_percent' => $this->cancel_fee_percent,
            'payment_requested_at' => $this->payment_requested_at,
            'paid_at' => $this->paid_at,
            'call_point' => $this->call_point,
            'nominee_point' => $this->nominee_point,
        ]);
    }
}
