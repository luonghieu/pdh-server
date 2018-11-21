<?php

namespace App\Http\Controllers\Admin\Order;

use App\Cast;
use App\CastClass;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentRequestStatus;
use App\Http\Controllers\Controller;
use App\Jobs\PointSettlement;
use App\Notification;
use App\Offer;
use App\Order;
use App\PaymentRequest;
use App\Services\LogService;
use Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Session;

class OrderController extends Controller
{
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

    public function castsMatching($order)
    {
        $order = Order::withTrashed()->find($order);

        if (empty($order)) {
            abort(404);
        }

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

    public function pointSettlement(Request $request, Order $order)
    {
        PointSettlement::dispatchNow($order->id);

        if ('order_nominee' == $request->page) {
            return redirect(route('admin.orders.order_nominee', compact('order')));
        } else {
            return redirect(route('admin.orders.call', compact('order')));
        }
    }

    public function offer(Request $request)
    {
        $castClasses = CastClass::all();

        $client = new Client(['base_uri' => config('common.api_url')]);
        $user = Auth::user();

        $accessToken = JWTAuth::fromUser($user);

        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        $classId = $request->cast_class;
        if (!isset($classId)) {
            $classId = 1;
        }
        $params = [
            'working_today' => 1,
            'page' => $request->get('page', 1),
            'latest' => 1,
            'class_id' => $classId,
        ];

        if ($request->search) {
            $params['search'] = $request->search;
        }

        try {
            $casts = $client->get(route('casts.index', $params), $option);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }

        $casts = json_decode(($casts->getBody())->getContents(), JSON_NUMERIC_CHECK);
        $casts = $casts['data'];
        $casts = new LengthAwarePaginator(
            $casts['data'],
            $casts['total'],
            $casts['per_page'],
            $casts['current_page'],
            [
                'query' => $request->all(),
                'path' => env('APP_URL') . '/admin/offer',
            ]
        );

        return view('admin.orders.offer', compact('casts', 'castClasses'));
    }

    public function confirmOffer(Request $request)
    {
        $data['cast_ids'] = $request->casts_offer;
        if (!isset($data['cast_ids'])) {
            $request->session()->flash('cast_not_found', 'cast_not_found');

            return redirect()->route('admin.offer.index');
        }

        $data['comment_offer'] = $request->comment_offer;
        if (!isset($data['comment_offer'])) {
            $request->session()->flash('message_exits', 'message_exits');

            return redirect()->route('admin.offer.index');
        }

        if (80 < strlen($data['comment_offer'])) {
            $request->session()->flash('message_invalid', 'message_invalid');

            return redirect()->route('admin.offer.index');
        }

        $data['start_time'] = $request->start_time_offer;
        $data['end_time'] = $request->end_time_offer;
        $data['date_offer'] = $request->date_offer;
        $data['duration_offer'] = $request->duration_offer;
        $data['area_offer'] = $request->area_offer;
        $data['current_point_offer'] = $request->current_point_offer;
        $data['class_id_offer'] = $request->class_id_offer;

        Session::put('offer', $data);

        $casts = Cast::whereIn('id', $data['cast_ids'])->get();

        return view('admin.orders.confirm_offer', compact('casts', 'data'));
    }

    public function price(Request $request, $offer = null)
    {
        $rules = $this->validate($request,
            [
                'date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'duration' => 'numeric|min:1|max:10',
                'class_id' => 'exists:cast_classes,id',
                'type' => 'required|in:1,2,3,4',

                'nominee_ids' => '',
                'total_cast' => 'required|numeric|min:1',
            ]
        );

        if (isset($request->offer)) {
            $offer = $request->offer;
        }

        $orderStartTime = Carbon::parse($request->date . ' ' . $request->start_time);
        $stoppedAt = $orderStartTime->copy()->addHours($request->duration);

        //nightTime

        $nightTime = 0;
        $allowanceStartTime = Carbon::parse('00:01:00');
        $allowanceEndTime = Carbon::parse('04:00:00');

        $startDay = Carbon::parse($orderStartTime)->startOfDay();
        $endDay = Carbon::parse($stoppedAt)->startOfDay();

        $timeStart = Carbon::parse(Carbon::parse($orderStartTime->format('H:i:s')));
        $timeEnd = Carbon::parse(Carbon::parse($stoppedAt->format('H:i:s')));

        $allowance = false;

        if ($startDay->diffInDays($endDay) != 0 && $stoppedAt->diffInMinutes($endDay) != 0) {
            $allowance = true;
        }

        if ($timeStart->between($allowanceStartTime, $allowanceEndTime) || $timeEnd->between($allowanceStartTime, $allowanceEndTime)) {
            $allowance = true;
        }

        if ($timeStart < $allowanceStartTime && $timeEnd > $allowanceEndTime) {
            $allowance = true;
        }

        if ($allowance) {
            $nightTime = $stoppedAt->diffInMinutes($endDay);
        }

        //allowance

        $totalCast = $request->total_cast;
        $allowancePoint = 0;
        if ($nightTime) {
            $allowancePoint = $totalCast * 4000;
        }

        //orderPoint

        $orderPoint = 0;
        $orderDuration = $request->duration * 60;
        $nomineeIds = explode(",", trim($request->nominee_ids, ","));

        if (OrderType::NOMINATION != $request->type) {
            $cost = CastClass::find($request->class_id)->cost;
            $orderPoint = $totalCast * (($cost / 2) * floor($orderDuration / 15));
        } else {
            $cost = Cast::find($nomineeIds[0])->cost;
            $orderPoint = ($cost / 2) * floor($orderDuration / 15);
        }

        //ordersFee

        $orderFee = 0;
        if (OrderType::NOMINATION != $request->type) {
            if (!isset($offer)) {
                if (!empty($nomineeIds[0])) {
                    $multiplier = floor($orderDuration / 15);
                    $orderFee = 500 * $multiplier * count($nomineeIds);
                }
            }
        }

        return ($orderPoint + $orderFee + $allowancePoint);
    }

    public function createOrder(Request $request)
    {
        if (!$request->session()->has('offer')) {
            return redirect()->route('admin.offer.index');
        }

        $data = Session::get('offer');

        $offer = new Offer;

        $offer->comment = $data['comment_offer'];
        $offer->date = $data['date_offer'];
        $offer->start_time_from = $data['start_time'];
        $offer->start_time_to = $data['end_time'];
        $offer->duration = $data['duration_offer'];
        $offer->total_cast = count($data['cast_ids']);
        $offer->prefecture_id = 13;
        $offer->temp_point = $data['current_point_offer'];
        $offer->class_id = $data['class_id_offer'];
        $offer->cast_ids = $data['cast_ids'];

        if (isset($request->save_temporarily)) {
            $offer->status = OfferStatus::INACTIVE;
        } else {
            $offer->status = OfferStatus::ACTIVE;
        }

        $offer->save();

        if ($request->session()->has('offer')) {
            $request->session()->forget('offer');
        }

        return redirect()->route('admin.offer.index');
    }
}
