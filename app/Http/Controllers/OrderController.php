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
        $client = new Client(['base_uri' => config('common.api_url')]);
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

        $client = new Client(['base_uri' => config('common.api_url')]);
        $user = Auth::user();

        $accessToken = JWTAuth::fromUser($user);

        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        try {
            $orderOptions = $client->get(route('glossaries'), $option);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }

        $orderOptions = json_decode(($orderOptions->getBody())->getContents(), JSON_NUMERIC_CHECK);
        $orderOptions = $orderOptions['data']['order_options'];

        return view('web.orders.create_call', compact('currentArea', 'currentTime', 'currentDuration', 'currentCastNumbers', 'currentCastClass', 'timeDetail', 'currentOtherArea', 'currentOtherDuration', 'orderOptions'));
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
                $timeJoin = 60;
            }

            $input['time'] = $timeJoin;
        }

        $duration = $request->time_set;

        if (!$duration || ('other_duration' != $duration && $duration <= 0)) {
            return redirect()->route('guest.orders.call');
        }

        if ('other_duration' == $duration) {
            if ($request->sl_duration < 0) {
                return redirect()->back();
            }
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

        $client = new Client(['base_uri' => config('common.api_url')]);

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

        if (isset($request->cast_ids)) {
            $data['casts'] = explode(",", $request->cast_ids);
        } else {
            if (isset($request->casts)) {
                $data['casts'] = $request->casts;
            } else {
                $data['casts'] = null;
            }
        }

        Session::put('data', $data);

        return redirect()->route('guest.orders.get_step4');
    }

    public function selectCasts(Request $request)
    {
        if (!$request->session()->has('data')) {
            return redirect()->route('guest.orders.call');
        }

        $data = Session::get('data');

        $client = new Client(['base_uri' => config('common.api_url')]);
        $user = Auth::user();

        $accessToken = JWTAuth::fromUser($user);

        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        try {
            $params = [
                'class_id' => $data['cast_class'],
                'latest' => 1,
                'order' => 1,
            ];

            $casts = $client->get(route('casts.index', $params), $option);
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

        if (isset($data['casts'])) {
            $castIds = implode(',', $data['casts']);
        } else {
            $castIds = null;
        }

        return view('web.orders.select_casts', compact('casts', 'currentCasts', 'castNumbers', 'castIds'));
    }

    public function attention(Request $request)
    {
        return view('web.orders.attention');
    }

    public function nominateAttention()
    {
        return view('web.orders.nominate_attention');
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

        if (isset($data['casts'])) {
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

        $data['type'] = $type;
        $data['nomineeIds'] = $nomineeIds;

        if (isset($data['otherTime'])) {
            $startDate = Carbon::parse($data['otherTime'])->format('Y-m-d');
            $startTime = Carbon::parse($data['otherTime'])->format('H:i');
        }

        if (isset($data['time'])) {
            $timeOrder = Carbon::now()->addMinutes($data['time']);

            $startDate = Carbon::parse($timeOrder)->format('Y-m-d');
            $startTime = Carbon::parse($timeOrder)->format('H:i');
        }

        $client = new Client(['base_uri' => config('common.api_url')]);
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

        $client = new Client(['base_uri' => config('common.api_url')]);
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
                'nominee_ids' => $data['nomineeIds'],
                'date' => $startDate,
                'start_time' => $startTime,
                'total_cast' => $data['cast_numbers'],
                'temp_point' => $data['temp_point'],
                'type' => $data['type'],
                'tags' => $tags,
            ]), $option);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            $statusCode = $e->getResponse()->getStatusCode();

            $request->session()->flash('statusCode', $statusCode);

            return redirect()->route('guest.orders.get_confirm');
        }

        $request->session()->flash('order_done', 'done');

        return redirect()->route('guest.orders.get_confirm');
    }

    public function history(Request $request, $orderId)
    {
        $user = Auth::user();
        $accessToken = JWTAuth::fromUser(Auth::user());
        $client = new Client();
        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        $response = $client->get(route('guest.get_payment_requests', ['id' => $orderId]), $option);
        $response = json_decode($response->getBody()->getContents());

        if (!$response->status) {
            return redirect()->back();
        }
        $order = $response->data;

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
            'base_uri' => config('common.api_url'),
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

        $client = new Client(['base_uri' => config('common.api_url')]);
        $user = Auth::user();

        $accessToken = JWTAuth::fromUser($user);

        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        try {
            $orderOptions = $client->get(route('glossaries'), $option);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }

        $orderOptions = json_decode(($orderOptions->getBody())->getContents(), JSON_NUMERIC_CHECK);
        $orderOptions = $orderOptions['data']['order_options'];

        return view('web.orders.nomination', compact('cast', 'user', 'orderOptions'));
    }

    public function createNominate(Request $request)
    {
        $area = $request->nomination_area;
        $otherArea = $request->other_area_nomination;
        if (!isset($area)) {
            return redirect()->back();
        }

        if ('その他' == $area && !$otherArea) {
            return redirect()->back();
        }

        if ('その他' == $area && $otherArea) {
            $area = $otherArea;
        }

        if (!$request->time_join_nomination) {
            return redirect()->back();
        }

        $now = Carbon::now();
        if ('other_time' == $request->time_join_nomination) {
            if ($request->sl_month_nomination < 10) {
                $month = '0' . $request->sl_month_nomination;
            } else {
                $month = $request->sl_month_nomination;
            }

            if ($request->sl_date_nomination < 10) {
                $date = '0' . $request->sl_date_nomination;
            } else {
                $date = $request->sl_date_nomination;
            }

            if ($request->sl_hour_nomination < 10) {
                $hour = '0' . $request->sl_hour_nomination;
            } else {
                $hour = $request->sl_hour_nomination;
            }

            if ($request->sl_minute_nomination < 10) {
                $minute = '0' . $request->sl_minute_nomination;
            } else {
                $minute = $request->sl_minute_nomination;
            }

            $date = $now->year . '-' . $month . '-' . $date;
            $time = $hour . ':' . $minute;
        } else {
            $now->addMinutes($request->time_join_nomination);

            $date = $now->format('Y-m-d');
            $time = $now->format('H:i');
        }

        $classId = $request->class_id;

        //duration
        $duration = $request->time_set_nomination;

        if (!$duration || ('other_time_set' != $duration && $duration <= 0)) {
            return redirect()->back();
        }

        if ('other_time_set' == $duration) {
            if ($request->sl_duration < 0) {
                return redirect()->back();
            }

            $duration = $request->sl_duration_nominition;
        }

        $client = new Client(['base_uri' => config('common.api_url')]);
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

            $order = json_decode(($order->getBody())->getContents(), JSON_NUMERIC_CHECK);
            $order = $order['data'];

            return redirect()->route('message.messages', ['room' => $order['room_id']]);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            $statusCode = $e->getResponse()->getStatusCode();

            $request->session()->flash('status_code', $statusCode);

            return redirect()->back();
        }
    }

    public function loadMoreListOrder(Request $request)
    {
        try {
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);

            $authorization = empty($token) ?: 'Bearer ' . $token;
            $client = new Client([
                'base_uri' => config('common.api_url'),
                'http_errors' => false,
                'debug' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => $authorization,
                    'Content-Type' => 'application/json',
                ],
            ]);
            $orders = $client->request('GET', request()->next_page);

            $orders = json_decode(($orders->getBody())->getContents(), JSON_NUMERIC_CHECK);
            $orders = $orders['data'];

            return [
                'next_page' => $orders['next_page_url'],
                'view' => view('web.orders.load_more_list_orders', compact('orders'))->render(),
            ];
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
    }

    public function loadMoreListCast(Request $request)
    {
        try {
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);

            $authorization = empty($token) ?: 'Bearer ' . $token;
            $client = new Client([
                'base_uri' => config('common.api_url'),
                'http_errors' => false,
                'debug' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => $authorization,
                    'Content-Type' => 'application/json',
                ],
            ]);
            $casts = $client->request('GET', request()->next_page);

            $casts = json_decode(($casts->getBody())->getContents(), JSON_NUMERIC_CHECK);
            $casts = $casts['data'];

            $currentCasts = null;
            if (isset(Session::get('data')['casts'])) {
                $currentCasts = Session::get('data')['casts'];
            }

            return [
                'next_page' => $casts['next_page_url'],
                'view' => view('web.orders.load_more_list_casts', compact('casts', 'currentCasts'))->render(),
            ];
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }
    }
}
