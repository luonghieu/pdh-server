<?php

namespace App\Http\Controllers\Admin\Order;

use App\Cast;
use App\CastClass;
use App\Enums\CastOrderStatus;
use App\Enums\CastOrderType;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentRequestStatus;
use App\Enums\PointType;
use App\Enums\RoomType;
use App\Http\Controllers\Controller;
use App\Jobs\PointSettlement;
use App\Notification;
use App\Notifications\AdminEditOrder;
use App\Notifications\AdminRemoveCastInOrder;
use App\Notifications\CallOrdersCreated;
use App\Notifications\CreateNominationOrdersForCast;
use App\Order;
use App\PaymentRequest;
use App\Point;
use App\Room;
use App\Services\LogService;
use App\Traits\DirectRoom;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use DirectRoom;

    public function index(Request $request)
    {
        $pointStatus = [
            OrderStatus::PROCESSING,
            OrderStatus::TIMEOUT,
            OrderStatus::DENIED,
            OrderStatus::CANCELED,
            OrderStatus::DONE,
            OrderStatus::ACTIVE,
            OrderStatus::OPEN,
        ];

        if ($request->has('notification_id')) {
            $notification = Notification::find($request->notification_id);
            if (null == $notification->read_at) {
                $now = Carbon::now();
                try {
                    $notification->read_at = $now;
                    $notification->save();
                } catch (\Exception $e) {
                    LogService::writeErrorLog($e);

                    return $this->respondServerError();
                }
            }
        }

        $keyword = $request->search;
        $orderBy = $request->only('user_id', 'id', 'type', 'address',
            'created_at', 'date', 'start_time', 'status');

        $orders = Order::with('user')->withTrashed();

        if ($request->has('from_date') && !empty($request->from_date)) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $orders->where(function ($query) use ($fromDate) {
                $query->whereDate('date', '>=', $fromDate);
            });
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $orders->where(function ($query) use ($toDate) {
                $query->whereDate('date', '<=', $toDate);
            });
        }

        if ($request->has('search') && $request->search) {
            $orders->where('id', $keyword)
                ->orWhereHas('user', function ($query) use ($keyword) {
                    $query->where('id', "$keyword")
                        ->orWhere('nickname', 'like', "%$keyword%");
                });
        }

        if (!$request->alert && empty($orderBy)) {
            $orders = $orders->orderBy('created_at', 'DESC');
        } else {
            switch ($request->alert) {
                case 'asc':
                    $orders = $orders->orderByRaw("FIELD(status, " . implode(',', $pointStatus) . ") ")
                        ->orderBy('date')->orderBy('start_time');
                    break;
                case 'desc':
                    $orders = $orders->orderByRaw("FIELD(status, " . implode(',', $pointStatus) . ") DESC ")
                        ->orderBy('date', 'DESC')->orderBy('start_time', 'DESC');
                    break;

                default:break;
            }

            if (!empty($orderBy)) {
                foreach ($orderBy as $key => $value) {
                    $orders->orderBy($key, $value);
                }
            }
        }

        $orders = $orders->paginate($request->limit ?: 10);

        return view('admin.orders.index', compact('orders'));
    }

    public function deleteOrder(Request $request)
    {
        if ($request->has('order_ids')) {
            $orderIds = array_map('intval', explode(',', $request->order_ids));

            $checkOrderIdExist = Order::whereIn('id', $orderIds)->exists();

            if ($checkOrderIdExist) {
                Order::whereIn('id', $orderIds)->delete();
            }
        }

        return redirect(route('admin.orders.index'));
    }

    public function nominees($order)
    {
        $order = Order::withTrashed()->find($order);

        if (empty($order)) {
            abort(404);
        }

        $casts = $order->nominees()->paginate();

        return view('admin.orders.nominees', compact('casts', 'order'));
    }

    public function candidates($order)
    {
        $order = Order::withTrashed()->find($order);

        if (empty($order)) {
            abort(404);
        }

        $casts = $order->candidates()->paginate();

        return view('admin.orders.candidates', compact('casts', 'order'));
    }

    public function orderCall(Request $request, $order)
    {
        $order = Order::withTrashed()->find($order);

        if (empty($order)) {
            abort(404);
        }

        if (OrderType::NOMINATION == $order->type) {
            $request->session()->flash('msg', trans('messages.order_not_found'));

            return redirect(route('admin.orders.index'));
        }

        $order = $order->load('candidates', 'nominees', 'user', 'castClass', 'room', 'casts', 'tags');

        return view('admin.orders.order_call', compact('order'));
    }

    public function editOrderCall(Order $order)
    {
        $castClasses = CastClass::all();
        if ($order->status == OrderStatus::ACTIVE || $order->status == OrderStatus::PROCESSING) {
            $castsMatching = $order->casts;
            $castsMatching = $castsMatching->map(function ($user) {
                return collect($user->toArray())
                    ->only(['id', 'nickname', 'pivot'])
                    ->all();
            });
            $castsNominee = [];
            $castsCandidates = [];
        } else {
            $castsNominee = $order->nominees()->whereNotIn('cast_order.status', [CastOrderStatus::TIMEOUT, CastOrderStatus::CANCELED])
                ->get();
            $castsNominee = $castsNominee->map(function ($user) {
                return collect($user->toArray())
                    ->only(['id', 'nickname', 'pivot'])
                    ->all();
            });
            $castsCandidates = $order->candidates()->whereNotIn('cast_order.status', [CastOrderStatus::TIMEOUT, CastOrderStatus::CANCELED])
                ->get();
            $castsCandidates = $castsCandidates->map(function ($user) {
                return collect($user->toArray())
                    ->only(['id', 'nickname', 'pivot'])
                    ->all();
            });

            $castsMatching = $order->casts;
            $castsMatching = $castsMatching->map(function ($user) {
                return collect($user->toArray())
                    ->only(['id', 'nickname', 'pivot'])
                    ->all();
            });
        }

        $castClass = CastClass::all();
        $orderTypeDesc = [
          OrderType::NOMINATED_CALL => OrderType::getDescription(OrderType::NOMINATED_CALL),
          OrderType::CALL => OrderType::getDescription(OrderType::CALL),
          OrderType::NOMINATION => OrderType::getDescription(OrderType::NOMINATION),
          OrderType::HYBRID => OrderType::getDescription(OrderType::HYBRID),
        ];
        $orderStatusDesc = [
            OrderStatus::OPEN => OrderStatus::getDescription(OrderStatus::OPEN),
            OrderStatus::ACTIVE => OrderStatus::getDescription(OrderStatus::ACTIVE),
            OrderStatus::PROCESSING => OrderStatus::getDescription(OrderStatus::PROCESSING),
            OrderStatus::DONE => OrderStatus::getDescription(OrderStatus::DONE),
            OrderStatus::TIMEOUT => OrderStatus::getDescription(OrderStatus::TIMEOUT),
        ];

        return view('admin.orders.order_call_edit', compact('order', 'castClasses', 'castsMatching', 'castsNominee', 'castsCandidates', 'orderTypeDesc', 'orderStatusDesc'));
    }

    public function updateOrderCall(Request $request, $id)
    {
        $order = Order::find($id);
        $orderDate = Carbon::parse($request->orderDate);

        try {
            \DB::beginTransaction();
            $order->duration = $request->orderDuration;
            $order->class_id = $request->class_id;
            $order->total_cast = $request->totalCast;
            $order->date = $orderDate->format('Y-m-d');
            $order->start_time = $orderDate->format('H:i');
            $order->type = $request->type;
            $order->temp_point = $request->temp_point;
            $order->status = $request->status;
            $order->save();

            $newNominees = [];
            $castsRemoved = [];
            $newMatchings = [];
            if ($request->addedNomineeCast) {
                $newNominees = Cast::whereIn('id', $request->addedNomineeCast)->get();
            }
            if ($request->addedCandidateCast) {
                $newMatchings = Cast::whereIn('id', $request->addedCandidateCast)->get();
            }

            if ($request->deletedCast) {
                $order->castOrder()->detach($request->deletedCast);
                $castsRemoved = Cast::whereIn('id', $request->deletedCast)->get();
            }

            $orderStartTime = Carbon::parse($order->date . ' ' . $order->start_time);
            $orderEndTime = $orderStartTime->copy()->addMinutes($order->duration * 60);
            $nightTime = $order->nightTime($orderEndTime);
            $allowance = $order->allowance($nightTime);
            $orderPoint = $order->orderPoint();

            // Update temp point for previous casts matched
            $matchedCasts = $order->casts;
            foreach ($matchedCasts as $cast) {
                if ($cast->pivot->type == CastOrderType::NOMINEE) {
                    $orderFee = $order->orderFee($cast, $orderStartTime, $orderEndTime);
                    $orderPoint = $order->orderPoint($cast);
                    $order->castOrder()->updateExistingPivot(
                        $cast->id,
                        [
                            'temp_point' => $orderPoint + $allowance + $orderFee,
                        ]
                    );
                } else {
                    $orderPoint = $order->orderPoint();
                    $order->castOrder()->updateExistingPivot(
                        $cast->id,
                        [
                            'temp_point' => $orderPoint + $allowance,
                        ]
                    );
                }
            }

            // Add casts nominee
            foreach ($newNominees as $nominee) {
                $order->nominees()->attach($nominee->id, [
                    'type' => CastOrderType::NOMINEE,
                    'status' => CastOrderStatus::OPEN,
                ]);
            }

            // Add casts matched
            foreach ($newMatchings as $matching) {
                $order->candidates()->attach($matching->id ,[
                    'type' => CastOrderType::CANDIDATE,
                    'status' => CastOrderStatus::ACCEPTED,
                    'accepted_at' => Carbon::now(),
                    'temp_point' => $orderPoint + $allowance,
                ]);
            }
            $currentTotalCast = $order->casts()->count();
            // Add/Remove casts in room
            $room = $order->room;
            if ($room) {
                if ($order->total_cast == 1) {
                    $cast = $order->casts()->first();
                    $ownerId = $order->user_id;
                    $room = $this->createDirectRoom($ownerId, $cast->id);
                    $room->save();

                    $order->room_id = $room->id;
                    $order->save();
                }

                if ($order->total_cast > 1) {
                    if ($room->type == RoomType::GROUP) {
                        $users = $order->casts()->get()->pluck('id')->toArray();
                        $users[] = $order->user_id;
                        $room->users()->sync($users);
                    } else {
                        $room = new Room;
                        $room->order_id = $order->id;
                        $room->owner_id = $order->user_id;
                        $room->type = RoomType::GROUP;
                        $room->save();
                        $users = $order->casts()->get()->pluck('id')->toArray();
                        $users[] = $order->user_id;
                        $room->users()->attach($users);

                        $order->room_id = $room->id;
                        $order->save();
                    }
                }
            } else {
                if ($order->total_cast == $currentTotalCast) {
                    if ($order->total_cast > 1) {
                        $room = new Room;
                        $room->order_id = $order->id;
                        $room->owner_id = $order->user_id;
                        $room->type = RoomType::GROUP;
                        $room->save();
                        $users = $order->casts()->get()->pluck('id')->toArray();
                        $users[] = $order->user_id;
                        $room->users()->attach($users);
                    }

                    if ($order->total_cast == 1) {
                        $cast = $order->casts()->first();
                        $ownerId = $order->user_id;
                        $room = $this->createDirectRoom($ownerId, $cast->id);
                        $room->save();
                    }

                    $order->room_id = $room->id;
                    $order->save();
                }
            }
            \DB::commit();

            // Send notification to new nominees
            \Notification::send(
                $newNominees,
                (new CreateNominationOrdersForCast($order->id))->delay(now()->addSeconds(3))
            );
            // Send notification to casts removed
            \Notification::send(
                $castsRemoved,
                (new AdminRemoveCastInOrder())->delay(now()->addSeconds(3))
            );
            // Send notification to user and casts.
            $order->user->notify((new AdminEditOrder())->delay(now()->addSeconds(3)));
            \Notification::send(
                $matchedCasts,
                (new AdminEditOrder())->delay(now()->addSeconds(3))
            );

            // Send notification to other casts
            if ($order->total_cast != $currentTotalCast) {
                $castInOrder = $order->castOrder()->get()->pluck('id')->toArray();
                $casts = Cast::where('class_id', $order->class_id)->whereNotIn('id', $castInOrder)->get();
                \Notification::send(
                    $casts,
                    (new CallOrdersCreated($order->id))->delay(now()->addSeconds(3))
                );
            }
            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'info' => $e->getMessage()], 400);
        }
    }

    public function getCasts(Request $request, $classId)
    {
        $casts = Cast::where('class_id', $classId);
        $listCast = [
            $request->listCastNominees,
            $request->listCastCandidates,
            $request->listCastMatching,
        ];
        $listCast = array_unique(collect($listCast)->collapse()->toArray());
        $search = $request->search;
        $casts->whereNotIn('id', $listCast);
        if ($search) {
            $casts->where(function($query) use ($search) {
                $query->where('nickname', 'like', "%$search%")
                    ->orWhere('id', $search);
            });
        }

        $casts = $casts->get();

        return response()->json([
            'view' => view('admin.orders.list_cast_by_class', compact('casts'))->render(),
            'casts' => $casts,
        ]);
    }

    public function castsMatching($order)
    {
        $order = Order::withTrashed()->find($order);

        if (empty($order)) {
            abort(404);
        }

        $casts = $order->casts;
        $paymentRequests = $order->paymentRequests->keyBy('cast_id')->toArray();

        return view('admin.orders.casts_matching', compact('casts', 'order', 'paymentRequests'));
    }

    public function changeStartTimeOrderCall(Request $request)
    {
        $order = Order::find($request->order_id);
        $castId = $request->cast_id;
        $casts = $order->casts;

        $newHour = $request->start_time_hour;
        $newMinute = $request->start_time_minute;
        $newDay = $request->start_date;
        $newStartTime = Carbon::parse($newDay . ' ' . $newHour . ':' . $newMinute);
        $this->changeStartTime($newStartTime, $order, $castId);

        return redirect(route('admin.orders.casts_matching', compact('casts', 'order')));
    }

    public function changeStopTimeOrderCall(Request $request)
    {
        $order = Order::find($request->order_id);
        $castId = $request->cast_id;
        $casts = $order->casts;

        $newHour = $request->stop_time_hour;
        $newMinute = $request->stop_time_minute;
        $newDay = $request->stop_date;
        $newstoppedTime = Carbon::parse($newDay . ' ' . $newHour . ':' . $newMinute);
        $cast = $order->casts()->withPivot('started_at', 'stopped_at', 'type')->where('user_id', $castId)->first();
        $startedDay = Carbon::parse($cast->pivot->started_at);
        if ($startedDay > $newstoppedTime) {
            $request->session()->flash('err', trans('messages.time_invalid'));

            return redirect(route('admin.orders.casts_matching', ['order' => $order->id]));
        }
        $this->changeStopTime($newstoppedTime, $order, $castId);

        return redirect(route('admin.orders.casts_matching', compact('casts', 'order')));
    }

    public function orderNominee(Request $request, $order)
    {
        $order = Order::withTrashed()->find($order);

        if (empty($order)) {
            abort(404);
        }

        if (OrderType::NOMINATION != $order->type) {
            $request->session()->flash('msg', trans('messages.order_not_found'));

            return redirect(route('admin.orders.index'));
        }

        return view('admin.orders.order_nominee', compact('order'));
    }

    public function changePaymentRequestStatus(Request $request, Order $order)
    {
        $order->payment_status = OrderPaymentStatus::WAITING;

        try {
            \DB::beginTransaction();
            $order->save();
            PaymentRequest::where([
                ['order_id', '=', $order->id],
                ['status', '=', PaymentRequestStatus::CONFIRM],
            ])->update(['status' => PaymentRequestStatus::OPEN]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }

        if ('order_nominee' == $request->page) {
            return redirect(route('admin.orders.order_nominee', compact('order')));
        } else {
            return redirect(route('admin.orders.call', compact('order')));
        }
    }

    public function changeStartTimeOrderNominee(Request $request)
    {
        $order = Order::find($request->orderId);
        $castId = $request->cast_id;
        $newHour = $request->start_time_hour;
        $newMinute = $request->start_time_minute;
        $newDay = $request->start_date;
        $newStartTime = Carbon::parse($newDay . ' ' . $newHour . ':' . $newMinute);
        $this->changeStartTime($newStartTime, $order, $castId);

        return redirect(route('admin.orders.order_nominee', compact('order')));
    }

    public function changeStopTimeOrderNominee(Request $request)
    {
        $order = Order::find($request->orderId);
        $castId = $request->cast_id;

        $newHour = $request->stop_time_hour;
        $newMinute = $request->stop_time_minute;
        $newDay = $request->stop_date;
        $newstoppedTime = Carbon::parse($newDay . ' ' . $newHour . ':' . $newMinute);
        $cast = $order->casts()->withPivot('started_at', 'stopped_at', 'type')->where('user_id', $castId)->first();
        $startedDay = Carbon::parse($cast->pivot->started_at);
        if ($startedDay > $newstoppedTime) {
            $request->session()->flash('err', trans('messages.time_invalid'));

            return redirect(route('admin.orders.order_nominee', ['order' => $order->id]));
        }
        $this->changeStopTime($newstoppedTime, $order, $castId);

        return redirect(route('admin.orders.order_nominee', compact('order')));
    }

    private function changeStartTime($newStartedTime, $order, $castId)
    {
        $cast = $order->casts()->withPivot('started_at', 'stopped_at', 'type')->where('user_id', $castId)->first();
        $stoppedAt = $cast->pivot->stopped_at;
        $totalTime = $newStartedTime->diffInMinutes($stoppedAt);
        $nightTime = $order->nightTime($stoppedAt);
        $extraTime = $order->extraTime($newStartedTime, $stoppedAt);
        $extraPoint = $order->extraPoint($cast, $extraTime);
        $orderPoint = $order->orderPoint($cast, $newStartedTime, $stoppedAt);
        $ordersFee = $order->orderFee($cast, $newStartedTime, $stoppedAt);
        $allowance = $order->allowance($nightTime);
        $totalPoint = $orderPoint + $ordersFee + $allowance + $extraPoint;
        $orderTime = (60 * $order->duration);

        $input = [
            'started_at' => $newStartedTime,
            'stopped_at' => $stoppedAt,
            'total_time' => $totalTime,
            'night_time' => $nightTime,
            'extra_time' => $extraTime,
            'extra_point' => $extraPoint,
            'order_point' => $orderPoint,
            'fee_point' => $ordersFee,
            'allowance_point' => $allowance,
            'total_point' => $totalPoint,
            'order_time' => $orderTime,
        ];

        $this->calculatorPoint($input, $castId, $order);
    }

    private function changeStopTime($newstoppedTime, $order, $castId)
    {
        $cast = $order->casts()->withPivot('started_at', 'stopped_at', 'type')->where('user_id', $castId)->first();
        $startedDay = Carbon::parse($cast->pivot->started_at);
        $extraTime = $order->extraTime($startedDay, $newstoppedTime);
        $nightTime = $order->nightTime($newstoppedTime);
        $extraPoint = $order->extraPoint($cast, $extraTime);
        $orderPoint = $order->orderPoint($cast, $startedDay, $newstoppedTime);
        $ordersFee = $order->orderFee($cast, $startedDay, $newstoppedTime);
        $allowance = $order->allowance($nightTime);
        $totalTime = $startedDay->diffInMinutes($newstoppedTime);
        $totalPoint = $orderPoint + $ordersFee + $allowance + $extraPoint;
        $orderTime = (60 * $order->duration);

        if ($startedDay < $newstoppedTime) {
            $input = [
                'started_at' => $startedDay,
                'stopped_at' => $newstoppedTime,
                'total_time' => $totalTime,
                'night_time' => $nightTime,
                'extra_time' => $extraTime,
                'extra_point' => $extraPoint,
                'order_point' => $orderPoint,
                'fee_point' => $ordersFee,
                'allowance_point' => $allowance,
                'total_point' => $totalPoint,
                'order_time' => $orderTime,
            ];

            $this->calculatorPoint($input, $castId, $order);
        }
    }

    private function calculatorPoint($input, $castId, $order)
    {
        try {
            \DB::beginTransaction();

            $order->casts()->updateExistingPivot($castId, $input, false);

            $latestStoppedAt = $input['stopped_at'];
            $earliesStartedtAt = $input['started_at'];

            if ($order->casts->count() > 1) {
                if ($order->actual_started_at > $earliesStartedtAt) {
                    $order->actual_started_at = $earliesStartedtAt;
                }

                if ($order->actual_ended_at < $latestStoppedAt) {
                    $order->actual_ended_at = $latestStoppedAt;
                }
            } else {
                $order->actual_started_at = $earliesStartedtAt;
                $order->actual_ended_at = $latestStoppedAt;
            }

            if (OrderType::NOMINATION != $order->type) {
                $totalPoint = 0;
                foreach ($order->casts as $cast) {
                    if ($cast->pivot->user_id != $castId) {
                        $totalPoint += $cast->pivot->total_point;
                    }
                }
                $order->total_point = $input['total_point'] + $totalPoint;
            } else {
                $order->total_point = $input['total_point'];
            }

            $order->save();

            $paymentRequest = $order->paymentRequests->where('cast_id', $castId)->first();

            if ($paymentRequest) {
                $paymentRequest->cast_id = $castId;
                $paymentRequest->guest_id = $order->user_id;
                $paymentRequest->order_id = $order->id;
                $paymentRequest->order_time = $input['order_time'];
                $paymentRequest->order_point = $input['order_point'];
                $paymentRequest->allowance_point = $input['allowance_point'];
                $paymentRequest->fee_point = $input['fee_point'];
                $paymentRequest->extra_time = $input['extra_time'];
                $paymentRequest->old_extra_time = $paymentRequest->extra_time;
                $paymentRequest->extra_point = $input['extra_point'];
                $paymentRequest->total_point = $input['total_point'];
                if ((OrderPaymentStatus::EDIT_REQUESTING == $order->payment_status) && (in_array($paymentRequest->status, [PaymentRequestStatus::REQUESTED, PaymentRequestStatus::UPDATED]))) {
                    $paymentRequest->status = PaymentRequestStatus::CONFIRM;
                }

                $paymentRequest->save();

                $point = Point::withTrashed()->where('payment_request_id', $paymentRequest->id)->where('type', PointType::TEMP)->first();
                if ($point) {
                    $castPercent = config('common.cast_percent');
                    $point->update(['point' => $paymentRequest->total_point * $castPercent]);
                }
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }

    public function pointSettlement(Request $request, Order $order)
    {

        PointSettlement::dispatchNow($order->id);

        if ('order_nominee' == $request->page) {
            return redirect(route('admin.orders.order_nominee', compact('order')));
        } else {
            return redirect(route('admin.orders.call', compact('order')));
        }
    }
}
