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
        $cast = $this->belongsToMany(Cast::class)->where('cast_order.status', CastOrderStatus::PROCESSING)
            ->withTimestamps()->withPivot('started_at', 'stopped_at', 'type')
            ->where('user_id', $userId)->first();

        $stoppedAt = Carbon::now();
        $orderStartTime = Carbon::parse($this->date . ' ' . $this->start_time);
        $orderTotalTime = $orderStartTime->diffInMinutes($stoppedAt);

        $nightTime = $this->nightTime($stoppedAt);
        $extraTime = $this->extraTime($stoppedAt);
        $extraPoint = $this->extraPoint($cast, $extraTime);
        $orderPoint = $this->orderPoint($cast);
        $ordersFee = ($cast->pivot->type == CastOrderType::NOMINEE) ? 3000 : 0;
        $allowance = $this->allowance($nightTime);

        try {
            $this->casts()->updateExistingPivot($userId, [
                'stopped_at' => $stoppedAt,
                'status' => CastOrderStatus::DONE,
                'night_time' => $nightTime,
                'extra_time' => $extraTime,
                'total_time' => $orderTotalTime,
                'fee_point' => $ordersFee,
                'allowance_point' => $allowance,
                'extra_point' => $extraPoint,
                'total_point' => $orderPoint + $ordersFee + $allowance + $extraPoint,
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

    private function nightTime($stoppedAt)
    {
        $order = $this;

        $nightTime = 0;
        $startDate = Carbon::parse($order->date . ' ' . $order->start_time);
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

    private function orderPoint($cast)
    {
        if ($this->type != OrderType::NOMINATION) {
            $cost = $this->castClass->cost;
        } else {
            $cost = $cast->cost;
        }

        return $cost;
    }

    private function extraTime($stoppedAt)
    {
        $order = $this;

        $extralTime = 0;
        $startDate = Carbon::parse($order->date . ' ' . $order->start_time);
        $endDate = $startDate->copy()->addMinutes($order->duration * 60);

        $castStoppedAt = Carbon::parse($stoppedAt);
        if ($castStoppedAt > $endDate) {
            $extralTime = $castStoppedAt->diffInMinutes($endDate);
        }

        return $extralTime;
    }

    private function extraPoint($cast, $extraTime)
    {
        $order = $this;
        $eTime = $extraTime;

        $extraPoint = 0;
        $multiplier = 0;
        if ($eTime > 15) {
            while ($eTime / 15 > 1) {
                $multiplier++;
                $eTime = $eTime - 15;
            }

            if ($order->type != OrderType::NOMINATION) {
                $costPerFifteenMins = $cast->castClass->cost / 2;
            } else {
                $costPerFifteenMins = $cast->cost / 2;
            }

            $extraPoint = ($costPerFifteenMins * 1.3) * $multiplier;
        }

        return $extraPoint;
    }
}
