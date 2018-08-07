<?php

namespace App;

use App\Enums\CastOrderStatus;
use App\Enums\CastOrderType;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PointType;
use App\Enums\RoomType;
use App\Jobs\CancelOrder;
use App\Jobs\ProcessOrder;
use App\Jobs\StopOrder;
use App\Jobs\ValidateOrder;
use App\Notifications\CastDenyNominationOrders;
use App\Services\LogService;
use App\Traits\DirectRoom;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use DirectRoom;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

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
            ->whereNull('cast_order.deleted_at')
            ->withPivot('order_time', 'extra_time', 'order_point', 'extra_point', 'allowance_point', 'fee_point', 'total_point', 'type', 'stopped_at')
            ->withTimestamps();
    }

    public function nominees()
    {
        return $this->belongsToMany(Cast::class)
            ->where('cast_order.type', CastOrderType::NOMINEE)
            ->whereNull('cast_order.deleted_at')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function candidates()
    {
        return $this->belongsToMany(Cast::class)
            ->where('cast_order.type', CastOrderType::CANDIDATE)
            ->whereNull('cast_order.deleted_at')
            ->withTimestamps();
    }

    public function castOrder()
    {
        return $this->belongsToMany(Cast::class)
            ->whereNull('cast_order.deleted_at')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function paymentRequests()
    {
        return $this->hasMany(PaymentRequest::class);
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
        return $this->belongsTo(Room::class);
    }

    public function deny($userId)
    {
        try {
            $this->nominees()->updateExistingPivot(
                $userId,
                ['status' => CastOrderStatus::DENIED, 'canceled_at' => Carbon::now()],
                false
            );
            if (OrderType::NOMINATION == $this->type) {
                $this->status = OrderStatus::DENIED;
                $this->save();

                $this->user->notify(new CastDenyNominationOrders($this));
            }

            ValidateOrder::dispatchNow($this);

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

            CancelOrder::dispatchNow($this);

            return true;
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return false;
        }
    }

    public function apply($userId)
    {
        try {
            $this->casts()->attach(
                $userId,
                [
                    'status' => CastOrderStatus::ACCEPTED,
                    'accepted_at' => Carbon::now(),
                    'type' => CastOrderType::CANDIDATE,
                ]
            );

            ValidateOrder::dispatchNow($this);

            return true;
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return false;
        }
    }

    public function accept($userId)
    {
        try {
            $this->nominees()->updateExistingPivot(
                $userId,
                ['status' => CastOrderStatus::ACCEPTED, 'accepted_at' => Carbon::now()],
                false
            );

            ValidateOrder::dispatchNow($this);

            return true;
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return false;
        }
    }

    public function stop($userId)
    {
        $cast = $this->casts()->withPivot('started_at', 'stopped_at', 'type')->where('user_id', $userId)->first();

        $stoppedAt = Carbon::now();
        $orderStartTime = Carbon::parse($this->date . ' ' . $this->start_time);
        $orderTotalTime = $orderStartTime->diffInMinutes($stoppedAt);

        $nightTime = $this->nightTime($stoppedAt);
        $extraTime = $this->extraTime($stoppedAt);
        $extraPoint = $this->extraPoint($cast, $extraTime);
        $orderPoint = $this->orderPoint($cast);
        $ordersFee = $this->orderFee($cast, $extraTime);
        $allowance = $this->allowance($nightTime);
        $totalPoint = $orderPoint + $ordersFee + $allowance + $extraPoint;

        try {
            \DB::beginTransaction();

            $this->casts()->updateExistingPivot($userId, [
                'stopped_at' => $stoppedAt,
                'status' => CastOrderStatus::DONE,
                'order_point' => $orderPoint,
                'order_time' => (60 * $this->duration),
                'night_time' => $nightTime,
                'extra_time' => $extraTime,
                'total_time' => $orderTotalTime,
                'fee_point' => $ordersFee,
                'allowance_point' => $allowance,
                'extra_point' => $extraPoint,
                'total_point' => $totalPoint,
            ], false);

            $paymentRequest = new PaymentRequest;
            $paymentRequest->cast_id = $userId;
            $paymentRequest->guest_id = $this->user_id;
            $paymentRequest->order_id = $cast->pivot->order_id;
            $paymentRequest->order_time = (60 * $this->duration);
            $paymentRequest->order_point = $orderPoint;
            $paymentRequest->allowance_point = $allowance;
            $paymentRequest->fee_point = $ordersFee;
            $paymentRequest->extra_time = $extraTime;
            $paymentRequest->old_extra_time = $extraTime;
            $paymentRequest->extra_point = $extraPoint;
            $paymentRequest->total_point = $totalPoint;
            $paymentRequest->save();

            \DB::commit();

            StopOrder::dispatchNow($this, $cast);

            return $paymentRequest;
        } catch (\Exception $e) {
            \DB::rollBack();
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

            $cast = User::find($userId);
            ProcessOrder::dispatchNow($this, $cast);

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

    public function isPaymentRequested()
    {
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            return null;
        }

        return ($this->paymentRequests()->where('cast_id', $user->id)->first()) ? 1 : 0;
    }

    public function nightTime($stoppedAt)
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

    public function allowance($nightTime)
    {
        if ($nightTime) {
            return 4000;
        }

        return 0;
    }

    public function orderPoint($cast)
    {
        if (OrderType::NOMINATION != $this->type) {
            $cost = $this->castClass->cost;
        } else {
            $cost = $cast->cost;
        }

        return $cost * ((60 * $this->duration) / 30);
    }

    public function orderFee($cast, $extraTime)
    {
        $order = $this;
        $eTime = $extraTime;
        $orderFee = 0;
        $multiplier = 0;
        $orderDuration = $this->duration * 60;
        if (OrderType::NOMINATION != $order->type && CastOrderType::NOMINEE == $cast->pivot->type) {
            while ($orderDuration / 15 >= 1) {
                $multiplier++;
                $orderDuration -= 15;
            }

            if ($eTime > 15) {
                while ($eTime / 15 > 1) {
                    $multiplier++;
                    $eTime -= 15;
                }
            }

            $orderFee = 500 * $multiplier;
            return $orderFee;
        }

        return $orderFee;
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

    public function extraPoint($cast, $extraTime)
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

            if (OrderType::NOMINATION != $order->type) {
                $costPerFifteenMins = $cast->castClass->cost / 2;
            } else {
                $costPerFifteenMins = $cast->cost / 2;
            }

            $extraPoint = ($costPerFifteenMins * 1.4) * $multiplier;
        }

        return $extraPoint;
    }

    public function getUserStatusAttribute()
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();

        $order = $this->castOrder->where('id', $user->id)->first();

        if (!$order) {
            return null;
        }

        return $order->pivot->status ?: 0;
    }

    public function getIsMatchingAttribute()
    {
        $matchingStatuses = [
            OrderStatus::ACTIVE,
            OrderStatus::PROCESSING,
            OrderStatus::DONE,
        ];

        return in_array($this->status, $matchingStatuses) ? 1 : 0;
    }

    public function getRoomIdAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->is_matching) {
            return '';
        }

        if ($this->total_cast > 1) {
            $room = Room::active()
                ->where('type', RoomType::GROUP)
                ->where('order_id', $this->id)
                ->first();

            if (!$room) {
                return '';
            }

            return $room->id;
        }

        $ownerId = $this->user_id;
        $castId = $this->casts()->first()->id;
        $room = $this->createDirectRoom($ownerId, $castId);

        return $room->id;
    }

    public function point()
    {
        return $this->hasOne(Point::class);
    }

    public function settle()
    {
        $user = $this->user;

        $point = new Point;

        $point->point = -$this->total_point;
        $point->balance = $user->point - $this->total_point;
        $point->user_id = $user->id;
        $point->order_id = $this->id;
        $point->type = PointType::PAY;
        $point->status = true;

        $point->save();

        $user->point = $point->balance;
        $user->save();

        return true;
    }
}
