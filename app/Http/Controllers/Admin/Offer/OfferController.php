<?php

namespace App\Http\Controllers\Admin\Offer;

use App\Cast;
use App\CastClass;
use App\Enums\OrderType;
use App\Http\Controllers\Controller;
use App\Offer;
use App\Services\LogService;
use Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use JWTAuth;
use Session;

class OfferController extends Controller
{
    public function create(Request $request)
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
                'path' => env('APP_URL') . '/admin/offers/create',
            ]
        );

        return view('admin.offers.create', compact('casts', 'castClasses'));
    }

    public function confirm(Request $request)
    {
        $data['cast_ids'] = $request->casts_offer;
        if (!isset($data['cast_ids'])) {
            $request->session()->flash('cast_not_found', 'cast_not_found');

            return redirect()->route('admin.offers.create');
        }

        $data['comment_offer'] = $request->comment_offer;
        if (!isset($data['comment_offer'])) {
            $request->session()->flash('message_exits', 'message_exits');

            return redirect()->route('admin.offers.create');
        }

        if (80 < strlen($data['comment_offer'])) {
            $request->session()->flash('message_invalid', 'message_invalid');

            return redirect()->route('admin.offers.create');
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

        return view('admin.offers.confirm', compact('casts', 'data'));
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

    public function store(Request $request)
    {
        if (!$request->session()->has('offer')) {
            return redirect()->route('admin.offers.create');
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

        return redirect()->route('admin.offers.create');
    }

    public function detail(Offer $offer)
    {
        $casts = Cast::whereIn('id', $offer->cast_ids)->get();

        return view('admin.offers.detail', compact('offer', 'casts'));
    }

    public function delete(Offer $offer)
    {
        $offer = Offer::findOrFail($offer->id);

        $offer->delete();

        return redirect()->route('admin.offers.create');
    }

    public function edit(Request $request, Offer $offer)
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
            $classId = $offer->class_id;
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
                'path' => env('APP_URL') . '/admin/offers/edit/' . $offer->id,
            ]
        );

        return view('admin.offers.edit', compact('offer', 'casts', 'castClasses'));
    }

    public function editConfirm(Request $request)
    {
        $data['cast_ids'] = $request->casts_offer;
        if (!isset($data['cast_ids'])) {
            $request->session()->flash('cast_not_found', 'cast_not_found');

            return redirect()->route('admin.offers.create');
        }

        $data['comment_offer'] = $request->comment_offer;
        if (!isset($data['comment_offer'])) {
            $request->session()->flash('message_exits', 'message_exits');

            return redirect()->route('admin.offers.create');
        }

        if (80 < strlen($data['comment_offer'])) {
            $request->session()->flash('message_invalid', 'message_invalid');

            return redirect()->route('admin.offers.create');
        }

        $data['start_time'] = $request->start_time_offer;
        $data['end_time'] = $request->end_time_offer;
        $data['date_offer'] = $request->date_offer;
        $data['duration_offer'] = $request->duration_offer;
        $data['area_offer'] = $request->area_offer;
        $data['current_point_offer'] = $request->current_point_offer;
        $data['class_id_offer'] = $request->class_id_offer;
        $data['offer_id'] = $request->offer_ids;

        Session::put('offerConfirm', $data);

        $casts = Cast::whereIn('id', $data['cast_ids'])->get();
        dd($data);
        return view('admin.offers.confirm', compact('casts', 'data'));
    }
}
