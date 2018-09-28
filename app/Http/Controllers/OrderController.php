<?php

namespace App\Http\Controllers;

use App\Cast;
use App\CastClass;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentRequestStatus;
use App\Enums\PointType;
use App\Enums\TagType;
use App\Enums\UserType;
use App\Order;
use App\Point;
use App\Services\LogService;
use App\Tag;
use App\Transfer;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use JWTAuth;
use Session;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $client = new Client();
        $user = Auth::user();

        $accessToken = JWTAuth::fromUser($user);

        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        try {
            $response = $client->get(route('guest.index', ['status' => OrderStatus::ACTIVE]), $option);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }

        $result = $response->getBody();
        $contents = $result->getContents();
        $contents = json_decode($contents, JSON_NUMERIC_CHECK);
        $orders = $contents['data'];

        return view('web.orders.list', compact('orders'));
    }

    public function call(Request $request)
    {
        $currentArea = null;
        if (isset(Session::get('data')['area'])) {
            $currentArea = Session::get('data')['area'];
        }

        $currentOtherArea = null;
        if (isset(Session::get('data')['other_area'])) {
            $currentOtherArea = Session::get('data')['other_area'];
        }

        $currentTime = null;
        if (isset(Session::get('data')['time'])) {
            $currentTime = Session::get('data')['time'];
        }

        $currentDuration = null;
        if (isset(Session::get('data')['duration'])) {
            $currentDuration = Session::get('data')['duration'];
        }

        $currentOtherDuration = null;
        if (isset(Session::get('data')['other_duration'])) {
            $currentOtherDuration = Session::get('data')['other_duration'];
        }

        $currentCastNumbers = null;
        if (isset(Session::get('data')['cast_numbers'])) {
            $currentCastNumbers = Session::get('data')['cast_numbers'];
        }

        $currentCastClass = null;
        if (isset(Session::get('data')['cast_class'])) {
            $currentCastClass = Session::get('data')['cast_class'];
        }

        $timeDetail = null;
        if (isset(Session::get('data')['time_detail'])) {
            $timeDetail = Session::get('data')['time_detail'];
        }

        return view('web.orders.create_call', compact('currentArea', 'currentTime', 'currentDuration', 'currentCastNumbers', 'currentCastClass', 'timeDetail', 'currentOtherArea', 'currentOtherDuration'));
    }

    public function getDayOfMonth(Request $request)
    {
        $month = $request->month;
        $now = Carbon::now();
        $number = cal_days_in_month(CAL_GREGORIAN, $month, $now->year);

        $data['month'] = $month;

        return getDay($data);
    }

    public function getParams(Request $request)
    {
        $input = [];
        $now = Carbon::now();
        $area = $request->area;
        $otherArea = $request->other_area;
        if (!$area && !$otherArea) {
            return redirect()->route('guest.orders.call');
        }

        if ('その他' == $area && $request->other_area) {
            $input['other_area'] = $otherArea;
        } else {
            $input['area'] = $area;
        }

        if (!$request->time_join) {
            return redirect()->route('guest.orders.call');
        }

        if ('other_time' == $request->time_join) {
            $timeDetail = [];

            if ($request->sl_month < 10) {
                $month = '0' . $request->sl_month;
            } else {
                $month = $request->sl_month;
            }

            $timeDetail['month'] = $month;

            if ($request->sl_date < 10) {
                $date = '0' . $request->sl_date;
            } else {
                $date = $request->sl_date;
            }

            $timeDetail['date'] = $date;

            if ($request->sl_hour < 10) {
                $hour = '0' . $request->sl_hour;
            } else {
                $hour = $request->sl_hour;
            }

            if ($request->sl_minute < 10) {
                $minute = '0' . $request->sl_minute;
            } else {
                $minute = $request->sl_minute;
            }

            $timeDetail['hour'] = $hour;

            $timeDetail['minute'] = $minute;

            $input['time_detail'] = $timeDetail;

            $timeJoin = $now->year . '-' . $month . '-' . $date . ' ' . $hour . ':' . $minute;
            $input['otherTime'] = $timeJoin;
        } else {
            $timeJoin = $request->time_join;
            if (!$timeJoin) {
                $timeJoin = 20;
            }

            $input['time'] = $timeJoin;
        }

        $duration = $request->time_set;

        if (!$duration || ('other_duration' != $duration && $duration <= 0)) {
            return redirect()->route('guest.orders.call');
        }

        if ('other_duration' == $duration) {
            $input['other_duration'] = $duration;

            $duration = $request->sl_duration;
        }

        $input['duration'] = $duration;

        $castNumbers = $request->txtCast_Number;
        if (!$castNumbers || $castNumbers <= 0) {
            return redirect()->route('guest.orders.call');
        }
        $input['cast_numbers'] = $castNumbers;

        $castClass = $request->cast_class;
        if (!$castClass) {
            return redirect()->route('guest.orders.call');
        }

        $input['cast_class'] = $castClass;

        Session::put('data', $input);

        return redirect()->route('guest.orders.get_step2');
    }

    public function selectTags(Request $request)
    {
        if (!$request->session()->has('data')) {
            return redirect()->route('guest.orders.call');
        }

        $client = new Client();

        try {
            $desires = $client->get(route('tags', ['type' => TagType::DESIRE]));

            $desires = json_decode(($desires->getBody())->getContents(), JSON_NUMERIC_CHECK);

            $situations = $client->get(route('tags', ['type' => TagType::SITUATION]));

            $situations = json_decode(($situations->getBody())->getContents(), JSON_NUMERIC_CHECK);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }

        $currentDesires = null;
        if (isset(Session::get('data')['desires'])) {
            $currentDesires = Session::get('data')['desires'];
        }

        $currentSituations = null;
        if (isset(Session::get('data')['situations'])) {
            $currentSituations = Session::get('data')['situations'];
        }

        return view('web.orders.set_tags', compact('desires', 'situations', 'currentDesires', 'currentSituations'));
    }

    public function getTags(Request $request)
    {
        if (!$request->session()->has('data')) {
            return redirect()->route('guest.orders.call');
        }
        $data = Session::get('data');

        $data['desires'] = $request->desires;

        $data['situations'] = $request->situations;

        Session::put('data', $data);

        return redirect()->route('guest.orders.get_step3');
    }

    public function getSelectCasts(Request $request)
    {
        if (!$request->session()->has('data')) {
            return redirect()->route('guest.orders.call');
        }

        $data = Session::get('data');
        $data['casts'] = $request->casts;

        Session::put('data', $data);

        return redirect()->route('guest.orders.get_step4');
    }

    public function selectCasts(Request $request)
    {
        if (!$request->session()->has('data')) {
            return redirect()->route('guest.orders.call');
        }

        $data = Session::get('data');

        $client = new Client();
        $user = Auth::user();

        $accessToken = JWTAuth::fromUser($user);

        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        try {
            $casts = $client->get(route('casts.index', ['working_today' => 1, 'class_id' => $data['cast_class']]), $option);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }

        $casts = json_decode(($casts->getBody())->getContents(), JSON_NUMERIC_CHECK);

        $casts = $casts['data'];

        $currentCasts = null;
        if (isset(Session::get('data')['casts'])) {
            $currentCasts = Session::get('data')['casts'];
        }

        $castNumbers = $data['cast_numbers'];

        return view('web.orders.select_casts', compact('casts', 'currentCasts', 'castNumbers'));
    }

    public function attention(Request $request)
    {
        return view('web.orders.attention');
    }

    public function getConfirm(Request $request)
    {
        $data = Session::get('data');
        if (!$request->session()->has('data')) {
            return redirect()->route('guest.orders.call');
        }

        $data = Session::get('data');

        $castClass = CastClass::findOrFail($data['cast_class']);

        $desires = [];
        $situations = [];

        if (isset($data['desires'])) {
            $desires = $data['desires'];
        }

        if (isset($data['situations'])) {
            $situations = $data['situations'];
        }

        $tags = Tag::whereIn('id', array_merge($desires, $situations))->get();

        if ($data['casts']) {
            $casts = Cast::whereIn('id', $data['casts'])->get();

            if (count($data['casts']) == $data['cast_numbers']) {
                $type = OrderType::NOMINATED_CALL;
            } else {
                $type = OrderType::HYBRID;
            }
            $nomineeIds = implode(',', $data['casts']);
        } else {
            $casts = [];
            $type = OrderType::CALL;
            $nomineeIds = '';
        }

        if (isset($data['otherTime'])) {
            $startDate = Carbon::parse($data['otherTime'])->format('Y-m-d');
            $startTime = Carbon::parse($data['otherTime'])->format('H:i');
        }

        if (isset($data['time'])) {
            $timeOrder = Carbon::now()->addMinutes($data['time']);

            $startDate = Carbon::parse($timeOrder)->format('Y-m-d');
            $startTime = Carbon::parse($timeOrder)->format('H:i');
        }

        $client = new Client();
        $user = Auth::user();

        $accessToken = JWTAuth::fromUser($user);

        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        try {
            $tempPoint = $client->post(route('orders.price', [
                'type' => $type,
                'class_id' => $data['cast_class'],
                'duration' => $data['duration'],
                'nominee_ids' => $nomineeIds,
                'date' => $startDate,
                'start_time' => $startTime,
                'total_cast' => $data['cast_numbers'],
            ]), $option);

            $tempPoint = json_decode(($tempPoint->getBody())->getContents(), JSON_NUMERIC_CHECK);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }

        $tempPoint = $tempPoint['data'];

        $data['temp_point'] = $tempPoint;
        $data['obj_tags'] = $tags;
        $data['obj_casts'] = $casts;
        $data['obj_cast_class'] = $castClass;
        Session::put('data', $data);

        return redirect()->route('guest.orders.get_confirm');
    }

    public function confirm(Request $request)
    {
        if (!$request->session()->has('data') || !isset(Session::get('data')['obj_casts'])) {
            return redirect()->route('guest.orders.call');
        }

        $user = \Auth::user();

        return view('web.orders.confirm_orders', compact('user'));
    }

    public function cancel()
    {
        return view('web.orders.cancel');
    }

    public function add(Request $request)
    {
        if (!$request->session()->has('data') || !isset(Session::get('data')['obj_casts'])) {
            return redirect()->route('guest.orders.call');
        }

        $data = Session::get('data');

        if (isset($data['otherTime'])) {
            $startDate = Carbon::parse($data['otherTime'])->format('Y-m-d');
            $startTime = Carbon::parse($data['otherTime'])->format('H:i');
        }

        if (isset($data['time'])) {
            $timeOrder = Carbon::now()->addMinutes($data['time']);

            $startDate = Carbon::parse($timeOrder)->format('Y-m-d');
            $startTime = Carbon::parse($timeOrder)->format('H:i');
        }

        if ($data['casts']) {
            if (count($data['casts']) == $data['cast_numbers']) {
                $type = OrderType::NOMINATED_CALL;
            } else {
                $type = OrderType::HYBRID;
            }
            $nomineeIds = implode(',', $data['casts']);
        } else {
            $type = OrderType::CALL;
            $nomineeIds = '';
        }

        $desires = [];
        $situations = [];

        if (isset($data['area'])) {
            $area = $data['area'];
        } else {
            $area = $data['other_area'];
        }

        if (isset($data['obj_tags'])) {
            $tags = [];

            foreach ($data['obj_tags'] as $val) {
                array_push($tags, $val->name);
            }

            $tags = implode(',', $tags);
        } else {
            $tags = '';
        }

        $client = new Client();
        $user = Auth::user();

        $accessToken = JWTAuth::fromUser($user);

        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        try {
            $order = $client->post(route('orders.create', [
                'prefecture_id' => 13,
                'address' => $area,
                'class_id' => $data['cast_class'],
                'duration' => $data['duration'],
                'nominee_ids' => $nomineeIds,
                'date' => $startDate,
                'start_time' => $startTime,
                'total_cast' => $data['cast_numbers'],
                'temp_point' => $data['temp_point'],
                'type' => $type,
                'tags' => $tags,
            ]), $option);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            $statusCode = $e->getResponse()->getStatusCode();

            $request->session()->flash('statusCode', $statusCode);

            return redirect()->route('guest.orders.get_confirm');
        }

        $request->session()->forget('data');

        return redirect()->route('web.index');
    }

    public function history(Request $request, $orderId)
    {
        $user = Auth::user();
        $order = Order::whereIn('status', [OrderStatus::DONE, OrderStatus::CANCELED])
            ->where('user_id', $user->id)
            ->with(['user', 'casts', 'nominees', 'tags'])
            ->find($orderId);

        if (!$order) {
            return redirect()->back();
        }

        return view('web.orders.history', compact('order', 'user'));
    }

    public function pointSettlement(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->card) {
            return response()->json(['success' => false], 400);
        }

        $now = Carbon::now();
        $order = Order::where('payment_status', OrderPaymentStatus::REQUESTING)->find($id);
        if (!$order) {
            return redirect()->back();
        }
        try {
            DB::beginTransaction();
            $order->settle();
            $order->paymentRequests()->update(['status' => PaymentRequestStatus::CLOSED]);

            $order->payment_status = OrderPaymentStatus::PAYMENT_FINISHED;
            $order->paid_at = $now;
            $order->update();

            $adminId = User::where('type', UserType::ADMIN)->first()->id;

            $order = $order->load('paymentRequests');

            $paymentRequests = $order->paymentRequests;

            $receiveAdmin = 0;
            $castPercent = config('common.cast_percent');

            foreach ($paymentRequests as $paymentRequest) {
                $receiveCast = $paymentRequest->total_point * $castPercent;
                $receiveAdmin += $paymentRequest->total_point * (1 - $castPercent);

                $this->createTransfer($order, $paymentRequest, $receiveCast);

                // receive cast
                $this->createPoint($receiveCast, $paymentRequest->cast_id, $order);
            }

            // receive admin
            $this->createPoint($receiveAdmin, $adminId, $order);

            DB::commit();

            return response()->json(['success' => true, 'message' => trans('messages.payment_completed')], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            LogService::writeErrorLog($e);
            return response()->json(['success' => false], 500);
        }
    }

    private function createTransfer($order, $paymentRequest, $receiveCast)
    {
        $transfer = new Transfer;
        $transfer->order_id = $order->id;
        $transfer->user_id = $paymentRequest->cast_id;
        $transfer->amount = $receiveCast;
        $transfer->save();
    }

    private function createPoint($receive, $id, $order)
    {
        $user = User::find($id);

        $point = new Point;
        $point->point = $receive;
        $point->balance = $user->point + $receive;
        $point->user_id = $user->id;
        $point->order_id = $order->id;
        $point->type = PointType::RECEIVE;
        $point->status = true;
        $point->save();

        $user->point += $receive;
        $user->update();
    }

    public function nominate(Request $request)
    {
        $id = $request->id;

        $user = Auth::user();
        $token = JWTAuth::fromUser($user);

        $authorization = empty($token) ?: 'Bearer ' . $token;

        $client = new Client([
            'http_errors' => false,
            'debug' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $authorization,
                'Content-Type' => 'application/json',
            ],
        ]);

        try {
            $cast = $client->get(route('users.show', $id));
            $cast = json_decode(($cast->getBody())->getContents(), JSON_NUMERIC_CHECK);
            $cast = $cast['data'];
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
        $user = \Auth::user();

        if (UserType::CAST != $cast['type']) {
            return redirect()->route('web.index');
        }

        return view('web.orders.nomination', compact('cast', 'user'));
    }

    public function createNominate(Request $request)
    {
        if (!isset($request->nomination_area)) {
            return redirect()->route('guest.orders.nominate');
        }

        $area = $request->nomination_area;
        $otherArea = $request->other_area_nomination;
        if (!$area && !$otherArea) {
            return redirect()->route('web.index');
        }

        if ('その他' == $area && $otherArea) {
            $area = $otherArea;
        }

        if (!$request->time_join_nomination) {
            return redirect()->route('web.index');
        }

        $now = Carbon::now();
        if ('other_time' == $request->time_join_nomination) {
            if ($request->sl_month < 10) {
                $month = '0' . $request->sl_month;
            } else {
                $month = $request->sl_month;
            }

            if ($request->sl_date < 10) {
                $date = '0' . $request->sl_date;
            } else {
                $date = $request->sl_date;
            }

            if ($request->sl_hour < 10) {
                $hour = '0' . $request->sl_hour;
            } else {
                $hour = $request->sl_hour;
            }

            if ($request->sl_minute < 10) {
                $minute = '0' . $request->sl_minute;
            } else {
                $minute = $request->sl_minute;
            }

            $date = $now->year . '-' . $month . '-' . $date;
            $time = $hour . ':' . $minute;
        } else {
            $now->addMinutes($request->time_join_nomination);

            $date = $now->format('Y-m-d');
            $time = $now->format('H:i');
        }

        $classId = $request->class_id;

        $duration = $request->time_set_nomination;
        if (4 == $duration) {
            $duration = $request->time_set_nomination;
        }

        if (!$duration || $duration <= 0) {
            return redirect()->route('web.index');
        }

        $client = new Client();
        $user = Auth::user();

        $accessToken = JWTAuth::fromUser($user);

        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        try {
            $tempPoint = $client->post(route('orders.price', [
                'type' => OrderType::NOMINATION,
                'class_id' => $classId,
                'duration' => $duration,
                'date' => $date,
                'start_time' => $time,
                'total_cast' => 1,
                'nominee_ids' => $request->cast_id,
            ]), $option);

            $tempPoint = json_decode(($tempPoint->getBody())->getContents(), JSON_NUMERIC_CHECK);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }

        $tempPoint = $tempPoint['data'];

        try {
            $order = $client->post(route('orders.create', [
                'prefecture_id' => 13,
                'address' => $area,
                'class_id' => $classId,
                'duration' => $duration,
                'date' => $date,
                'start_time' => $time,
                'total_cast' => 1,
                'temp_point' => $tempPoint,
                'type' => OrderType::NOMINATION,
                'nominee_ids' => $request->cast_id,
            ]), $option);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }

        return redirect()->route('web.index');
    }
}
