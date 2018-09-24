<?php

namespace App\Http\Controllers;

use App\Cast;
use App\CastClass;
use App\Enums\OrderType;
use App\Enums\TagType;
use App\Http\Controllers\Controller;
use App\Services\LogService;
use App\Tag;
use Auth;
use Carbon\Carbon;
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
            $response = $client->get(route('guest.index'), $option);
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

        $currentTime = null;
        if (isset(Session::get('data')['time'])) {
            $currentTime = Session::get('data')['time'];
        }

        $currentDuration = null;
        if (isset(Session::get('data')['duration'])) {
            $currentDuration = Session::get('data')['duration'];
        }

        $currentCastNumbers = null;
        if (isset(Session::get('data')['cast_numbers'])) {
            $currentCastNumbers = Session::get('data')['cast_numbers'];
        }

        $currentCastClass = null;
        if (isset(Session::get('data')['cast_class'])) {
            $currentCastClass = Session::get('data')['cast_class'];
        }

        return view('web.orders.create_call', compact('currentArea', 'currentTime', 'currentDuration', 'currentCastNumbers', 'currentCastClass'));
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

        if ($area && $request->other_area) {
            $area = $request->other_area;
        }

        if (!$area) {
            return redirect()->route('guest.orders.call');
        }

        $input['area'] = $area;

        if (!$request->time_join) {
            return redirect()->route('guest.orders.call');
        }

        if ('other_time' == $request->time_join) {
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

            $timeJoin = $now->year . '-' . $month . '-' . $date . ' ' . $hour . ':' . $minute;
            $input['otherTime'] = $timeJoin;
        } else {
            $timeJoin = $request->time_join;
            $input['time'] = $timeJoin;
        }

        $duration = $request->time_set;
        if (4 == $duration) {
            $duration = $request->sl_duration;
        }

        if (!$duration || $duration <= 0) {
            return redirect()->route('guest.orders.call');
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

        $desires = $client->get(route('tags', ['type' => TagType::DESIRE]));

        $desires = json_decode(($desires->getBody())->getContents(), JSON_NUMERIC_CHECK);

        $situations = $client->get(route('tags', ['type' => TagType::SITUATION]));

        $situations = json_decode(($situations->getBody())->getContents(), JSON_NUMERIC_CHECK);

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

        if (isset($data['desires'])) {
            $desires = $data['desires'];
        }

        if (isset($data['situations'])) {
            $situations = $data['situations'];
        }
        $tags = array_merge($desires, $situations);
        $tags = implode(',', $tags);

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
                'address' => $data['area'],
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

            $order = json_decode(($order->getBody())->getContents(), JSON_NUMERIC_CHECK);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            abort(500);
        }

        $request->session()->forget('data');

        $order = $order['data'];
    }
}
