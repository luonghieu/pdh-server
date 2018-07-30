<?php

namespace App;

use App\Enums\CastOrderStatus;
use App\Enums\CastOrderType;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Jobs\ValidateOrder;
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
            ->where('cast_order.status', CastOrderStatus::ACCEPTED)->withTimestamps();
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
            return false;
        }
    }

    public function stop($userId)
    {
        try {
            $this->casts()->updateExistingPivot($userId, [
                'stopped_at' => Carbon::now(),
                'status' => CastOrderStatus::DONE,
                'point' => $this->receivePoint($userId)
            ], false);

            return true;
        } catch (\Exception $e) {
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
        $order = $this;
        $allowanceStartTime = Carbon::parse('00:01:00');
        $allowanceEndTime = Carbon::parse('04:00:00');

        $orderStartDate = Carbon::parse($order->date . ' ' .$order->start_time);
        $orderEndDate = Carbon::now();

        $startDay = Carbon::parse($orderStartDate)->startOfDay();
        $endDay = $orderEndDate->copy()->startOfDay();

        $startTime = Carbon::parse(Carbon::parse($orderStartDate->format('H:i:s')));
        $endTime = Carbon::parse(Carbon::parse($orderEndDate->format('H:i:s')));

        $allowance = 4000;
        if ($startDay->diffInDays($endDay) != 0 && $orderEndDate->diffInMinutes($endDay) != 0) {
            return $allowance;
        }

        if ($startTime->between($allowanceStartTime, $allowanceEndTime) || $endTime->between($allowanceStartTime,
                $allowanceEndTime)) {
            return $allowance;
        }

        if ($startTime < $allowanceStartTime  && $endTime > $allowanceEndTime) {
            return $allowance;
        }

        return 0;
    }

    private function extraPoint($cast)
    {
        $order = $this;
        $extralTime = 0;
        $orderStartDate = Carbon::parse($order->date . ' ' . $order->start_time);
        $orderEndDate = $orderStartDate->copy()->addMinutes($order->duration * 60);

        $castStoppedAt = Carbon::now();
        if ($castStoppedAt > $orderEndDate) {
            $extralTime = $castStoppedAt->diffInMinutes($orderEndDate);
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

            return ($costPerFifteenMins * 1.3) * $multiplier;
        }

        return 0;
    }

    private function receivePoint($castId)
    {
        $order = $this;
        $cast = $order->belongsToMany(Cast::class)->where('cast_order.status', CastOrderStatus::PROCESSING)
            ->withTimestamps()->withPivot('started_at', 'stopped_at')
            ->where('user_id', $castId)->first();

        if ($cast) {
            if ($order->type != OrderType::NOMINATION) {
                $cost = $order->castClass->cost;
            } else {
                $cost = $cast->cost;
            }

            $orderPoint = $cost * ((60 * $order->duration) / 30);
            $allowance = $this->allowance();
            $extraPoint = $this->extraPoint($cast);

            $ordersFee = ($order->type == OrderType::CALL) ? 0 : 3000;

            return $orderPoint + $ordersFee + $allowance + $extraPoint;
        }

        return null;
    }
}
