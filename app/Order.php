<?php

namespace App;

use App\Enums\CastOrderStatus;
use App\Enums\CastOrderType;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Jobs\ValidateOrder;
use App\Services\LogService;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'prefecture_id',
        'address',
        'date',
        'start_time',
        'end_time',
        'duration',
        'total_cast',
        'temp_point',
        'class_id',
        'type',
        'status',
        'canceled_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function casts()
    {
        return $this->belongsToMany(Cast::class)
            ->whereNotNull('cast_order.accepted_at')
            ->whereNull('cast_order.canceled_at')
            ->withTimestamps();
    }

    public function nominees()
    {
        return $this->belongsToMany(Cast::class)
            ->where('cast_order.type', CastOrderType::NOMINEE)->withPivot('status')->withTimestamps();
    }

    public function candidates()
    {
        return $this->belongsToMany(Cast::class)
            ->where('cast_order.type', CastOrderType::CANDIDATE)->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function castClass()
    {
        return $this->hasOne(CastClass::class, 'id', 'class_id');
    }

    public function room()
    {
        return $this->hasOne(Room::class);
    }

    public function deny($userId)
    {
        try {
            $this->nominees()->updateExistingPivot($userId,
                ['status' => CastOrderStatus::DENIED, 'canceled_at' => Carbon::now()], false);

            ValidateOrder::dispatch($this);

            return true;
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return false;
        }
    }

    public function cancel()
    {
        try {
            $this->update([
                'status' => OrderStatus::CANCELED,
                'canceled_at' => Carbon::now(),
            ]);

            return true;
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return false;
        }
    }

    public function apply($userId)
    {
        try {
            $this->casts()->attach($userId,
                [
                    'status' => CastOrderStatus::ACCEPTED,
                    'accepted_at' => Carbon::now(),
                    'type' => CastOrderType::CANDIDATE,
                ]);

            ValidateOrder::dispatch($this);

            return true;
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return false;
        }
    }

    public function accept($userId)
    {
        try {
            $this->nominees()->updateExistingPivot($userId,
                ['status' => CastOrderStatus::ACCEPTED, 'accepted_at' => Carbon::now()], false);

            ValidateOrder::dispatch($this);

            return true;
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return false;
        }
    }

    public function stop($userId)
    {
        try {
            $castStopDetails = $this->getCastStopOrderDetails($userId);
            $this->casts()->updateExistingPivot($userId, [
                'stopped_at' => Carbon::now(),
                'status' => CastOrderStatus::DONE,
                'night_time' => $castStopDetails->night_time,
                'extra_time' => $castStopDetails->extra_time,
                'total_time' => $castStopDetails->total_time,
                'fee_point' => $castStopDetails->fee_point,
                'allowance_point' => $castStopDetails->allowance_point,
                'extra_point' => $castStopDetails->extra_point,
                'total_point' => $castStopDetails->total_point,
            ], false);

            return true;
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return false;
        }
    }

    public function start($userId)
    {
        try {
            $this->casts()->updateExistingPivot($userId, [
                'started_at' => Carbon::now(),
                'status' => CastOrderStatus::PROCESSING,
            ], false);

            return true;
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return false;
        }
    }

    public function isNominated()
    {
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            return null;
        }

        return ($this->nominees()->where('user_id', $user->id)->first()) ? 1 : 0;
    }

    private function allowance()
    {
        $allowanceInfo = (object)[
            'night_time' => 0,
            'allowance' => 0
        ];

        $order = $this;
        $allowanceStartTime = Carbon::parse('00:01:00');
        $allowanceEndTime = Carbon::parse('04:00:00');

        $orderStartDate = Carbon::parse($order->date . ' ' .$order->start_time);
        $orderEndDate = Carbon::now();

        $startDay = Carbon::parse($orderStartDate)->startOfDay();
        $endDay = $orderEndDate->copy()->startOfDay();

        $startTime = Carbon::parse(Carbon::parse($orderStartDate->format('H:i:s')));
        $endTime = Carbon::parse(Carbon::parse($orderEndDate->format('H:i:s')));

        $isAllowance = false;
        if ($startDay->diffInDays($endDay) != 0 && $orderEndDate->diffInMinutes($endDay) != 0) {
            $isAllowance = true;
        }

        if ($startTime->between($allowanceStartTime, $allowanceEndTime) || $endTime->between($allowanceStartTime, $allowanceEndTime)) {
            $isAllowance = true;
        }

        if ($startTime < $allowanceStartTime  && $endTime > $allowanceEndTime) {
            $isAllowance = true;
        }

        if ($isAllowance) {
            $allowanceInfo->night_time = $orderEndDate->diffInMinutes($endDay);
            $allowanceInfo->allowance = 4000;
        }

        return $allowanceInfo;
    }

    private function extraInfo($cast)
    {
        $extraInfo = (object)[
            'extra_time' => 0,
            'extra_point' => 0
        ];

        $order = $this;
        $extralTime = 0;
        $orderStartDate = Carbon::parse($order->date . ' ' . $order->start_time);
        $orderEndDate = $orderStartDate->copy()->addMinutes($order->duration * 60);

        $castStoppedAt = Carbon::now();
        if ($castStoppedAt > $orderEndDate) {
            $extralTime = $castStoppedAt->diffInMinutes($orderEndDate);
            $extraInfo->extra_time = $extralTime;
        }

        $multiplier = 0;
        if ($extralTime > 15) {
            while ($extralTime / 15 > 1) {
                $multiplier++;
                $extralTime = $extralTime - 15;
            }

            if ($order->type != OrderType::NOMINATION) {
                $costPerFifteenMins = $order->castClass->cost / 2;
            } else {
                $costPerFifteenMins = $cast->cost / 2;
            }

            $extraInfo->extra_point = ($costPerFifteenMins * 1.3) * $multiplier;

            return $extraInfo;
        }

        return $extraInfo;
    }

    private function getCastStopOrderDetails($castId)
    {
        $order = $this;
        $cast = $order->belongsToMany(Cast::class)->where('cast_order.status', CastOrderStatus::PROCESSING)
            ->withTimestamps()->withPivot('started_at', 'stopped_at', 'type')
            ->where('user_id', $castId)->first();

        if ($cast) {
            if ($order->type != OrderType::NOMINATION) {
                $cost = $order->castClass->cost;
            } else {
                $cost = $cast->cost;
            }

            $orderPoint = $cost * ((60 * $order->duration) / 30);
            $allowanceInfo = $this->allowance();
            $extraInfo = $this->extraInfo($cast);

            $ordersFee = ($cast->pivot->type == CastOrderType::NOMINEE) ? 3000 : 0;

            $orderStartTime = Carbon::parse($order->date . ' ' . $order->start_time);
            $orderTotalTime = $orderStartTime->diffInMinutes(Carbon::now());

            return (object) [
                'night_time' => $allowanceInfo->night_time,
                'extra_time' => $extraInfo->extra_time,
                'total_time' => $orderTotalTime,
                'fee_point' => $ordersFee,
                'allowance_point' => $allowanceInfo->allowance,
                'extra_point' => $extraInfo->extra_point,
                'total_point' => $orderPoint + $ordersFee + $allowanceInfo->allowance + $extraInfo->extra_point,
            ];
        }

        return null;
    }
}
