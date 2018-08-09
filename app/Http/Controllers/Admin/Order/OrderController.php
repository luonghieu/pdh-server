<?php

namespace App\Http\Controllers\Admin\Order;

use App\Enums\OrderPaymentStatus;
use App\Enums\PaymentRequestStatus;
use App\Http\Controllers\Controller;
use App\Order;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $keyword = $request->search;

        $orders = Order::with('user');

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

        if ($request->has('search')) {
            $orders->where('id', $keyword)
                ->orWhereHas('user', function ($query) use ($keyword) {
                    $query->where('id', "$keyword")
                        ->orWhere('nickname', 'like', "%$keyword%");
                });
        }

        $orders = $orders->orderBy('created_at', 'DESC')->paginate($request->limit ?: 10);

        return view('admin.orders.index', compact('orders'));
    }

    public function deleteOrder(Request $request)
    {
        if ($request->has('order_ids')) {
            $orderIds = $request->order_ids;

            $checkOrderIdExist = Order::whereIn('id', $orderIds)->exists();

            if ($checkOrderIdExist) {
                Order::whereIn('id', $orderIds)->delete();
            }
        }

        return redirect(route('admin.orders.index'));
    }

    public function nominees(Order $order)
    {
        $casts = $order->nominees()->paginate();

        return view('admin.orders.nominees', compact('casts', 'order'));
    }

    public function candidates(Order $order)
    {
        $casts = $order->candidates()->paginate();

        return view('admin.orders.candidates', compact('casts', 'order'));
    }

    public function orderCall(Order $order)
    {
        $order = $order->load('candidates', 'nominees', 'user', 'castClass', 'room', 'casts', 'tags');

        return view('admin.orders.order_call', compact('order'));
    }

    public function castsMatching(Order $order)
    {
        $casts = $order->casts;

        return view('admin.orders.casts_matching', compact('casts', 'order'));
    }

    public function changeStartTimeOrderCall(Request $request)
    {
        $order = Order::find($request->order_id);
        $castId = $request->cast_id;
        $casts = $order->casts;

        $newHour = $request->start_time_hour;
        $newMinute = $request->start_time_minute;
        $this->changeStartTime($newHour, $newMinute, $order, $castId);

        return redirect(route('admin.orders.casts_matching', compact('casts', 'order')));
    }

    public function changeStopTimeOrderCall(Request $request)
    {
        $order = Order::find($request->order_id);
        $castId = $request->cast_id;
        $casts = $order->casts;

        $newHour = $request->stop_time_hour;
        $newMinute = $request->stop_time_minute;
        $this->changeStopTime($newHour, $newMinute, $order, $castId);

        return redirect(route('admin.orders.casts_matching', compact('casts', 'order')));
    }

    public function orderNominee(Order $order)
    {
        return view('admin.orders.order_nominee', compact('order'));
    }

    public function changePaymentRequestStatus(Order $order)
    {
        $order->payment_status = OrderPaymentStatus::WAITING;
        try {
            $order->save();
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
        return view('admin.orders.order_nominee', compact('order'));
    }

    public function changeStartTimeOrderNominee(Request $request)
    {
        $order = Order::find($request->orderId);
        $castId = $request->cast_id;
        $newHour = $request->start_time_hour;
        $newMinute = $request->start_time_minute;
        $this->changeStartTime($newHour, $newMinute, $order, $castId);

        return redirect(route('admin.orders.order_nominee', compact('order')));
    }

    public function changeStopTimeOrderNominee(Request $request)
    {
        $order = Order::find($request->orderId);
        $castId = $request->cast_id;

        $newHour = $request->stop_time_hour;
        $newMinute = $request->stop_time_minute;
        $this->changeStopTime($newHour, $newMinute, $order, $castId);

        return redirect(route('admin.orders.order_nominee', compact('order')));
    }

    private function changeStartTime($newHour, $newMinute, $order, $castId)
    {
        $cast = $order->casts()->withPivot('started_at', 'stopped_at', 'type')->where('user_id', $castId)->first();
        $startedDay = Carbon::parse($cast->pivot->started_at)->format('Y-m-d');
        $newStartedTime = Carbon::parse($startedDay . ' ' . $newHour . ':' . $newMinute);
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

    private function changeStopTime($newHour, $newMinute, $order, $castId)
    {
        $cast = $order->casts()->withPivot('started_at', 'stopped_at', 'type')->where('user_id', $castId)->first();
        $startedDay = Carbon::parse($cast->pivot->started_at);
        $stoppedDay = Carbon::parse($cast->pivot->stopped_at)->format('Y-m-d');
        $newstoppedTime = Carbon::parse($stoppedDay . ' ' . $newHour . ':' . $newMinute);
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
        };
    }

    private function calculatorPoint($input, $castId, $order)
    {
        try {
            \DB::beginTransaction();
            $order->casts()->updateExistingPivot($castId, $input, false);

            $paymentRequest = $order->paymentRequests->first();

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
                $paymentRequest->status = PaymentRequestStatus::CONFIRM;
                $paymentRequest->save();
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }
}
