<?php

namespace App\Http\Controllers\Api\Guest;

use App\Cast;
use App\CastOffer;
use App\Coupon;
use App\Enums\CastOfferStatus;
use App\Enums\CastOrderStatus;
use App\Enums\CastOrderType;
use App\Enums\CouponType;
use App\Enums\InviteCodeHistoryStatus;
use App\Enums\OrderPaymentMethod;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\CastOfferResource;
use App\Http\Resources\OrderResource;
use App\Notifications\CreateNominationOrdersForCast;
use App\Notifications\GuestCancelOrderOfferFromCast;
use App\Point;
use App\Services\LogService;
use App\Traits\DirectRoom;
use App\User;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use JWTAuth;

class CastOfferController extends ApiController
{
    use DirectRoom;

    public function show($id)
    {
        $user = $this->guard()->user();
        $offer = CastOffer::where('status', CastOfferStatus::PENDING)->where('guest_id', $user->id)->find($id);

        if (!$offer) {
            return $this->respondErrorMessage(trans('messages.offer_not_found'), 404);
        }

        $start_time = Carbon::parse($offer->date . ' ' . $offer->start_time);
        if (now()->second(0)->diffInMinutes($start_time, false) < 29) {
            return $this->respondErrorMessage(trans('messages.order_timeout'), 400);
        }

        return $this->respondWithData(new CastOfferResource($offer));
    }

    public function cancel($id)
    {
        $user = $this->guard()->user();
        $offer = CastOffer::where('status', CastOfferStatus::PENDING)->where('guest_id', $user->id)->find($id);

        if (!$offer) {
            return $this->respondErrorMessage(trans('messages.offer_not_found'), 404);
        }

        $start_time = Carbon::parse($offer->date . ' ' . $offer->start_time);
        if (now()->second(0)->diffInMinutes($start_time, false) < 29) {
            return $this->respondErrorMessage(trans('messages.order_timeout'), 400);
        }

        try {
            $offer->status = CastOfferStatus::DENIED;
            $offer->save();

            \Notification::send($offer->cast, new GuestCancelOrderOfferFromCast($offer));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
        }

        return $this->respondWithData(new CastOfferResource($offer));
    }

    public function create(Request $request)
    {
        $user = $this->guard()->user();

        $rules = [
            'prefecture_id' => 'nullable|exists:prefectures,id',
            'address' => 'required',
            'date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'duration' => 'required|numeric|min:1|max:10',
            'total_cast' => 'required|numeric|min:1',
            'temp_point' => 'required',
            'class_id' => 'required|exists:cast_classes,id',
            'type' => 'required|in:1,2,3,4',
            'tags' => '',
            'nominee_ids' => '',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $offer = CastOffer::where('status', CastOfferStatus::PENDING)->where('guest_id', $user->id)->find($request->cast_offer_id);

        if (!$user->status) {
            return $this->respondErrorMessage(trans('messages.freezing_account'), 403);
        }

        if (!$offer) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        $transfer = $request->payment_method;

        if (isset($transfer)) {
            if (OrderPaymentMethod::CREDIT_CARD == $transfer || OrderPaymentMethod::DIRECT_PAYMENT == $transfer) {
                if (OrderPaymentMethod::DIRECT_PAYMENT == $transfer) {
                    $accessToken = JWTAuth::fromUser($user);

                    $client = new Client([
                        'base_uri' => config('common.api_url'),
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $accessToken,
                        ],
                    ]);

                    try {
                        $pointUsed = $client->request('GET', route('guest.points_used'));

                        $pointUsed = json_decode(($pointUsed->getBody())->getContents(), JSON_NUMERIC_CHECK);
                        $pointUsed = $pointUsed['data'];
                    } catch (\Exception $e) {
                        return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
                    }

                    if ((float) ($request->temp_point + $pointUsed) > (float) $user->point) {
                        return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
                    }
                }
            } else {
                return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
            }
        }

        $input = $request->only([
            'prefecture_id',
            'address',
            'date',
            'start_time',
            'duration',
            'total_cast',
            'temp_point',
            'class_id',
            'type',
            'cast_offer_id',
        ]);

        $start_time = Carbon::parse($offer->date . ' ' . $offer->start_time);
        $end_time = $start_time->copy()->addHours((int) $offer->duration);

        if (now()->second(0)->diffInMinutes($start_time, false) < 29) {
            return $this->respondErrorMessage(trans('messages.time_invalid'), 400);
        }

        if (!$request->payment_method || OrderPaymentMethod::DIRECT_PAYMENT != $request->payment_method) {
            if (!$user->is_card_registered) {
                return $this->respondErrorMessage(trans('messages.card_not_exist'), 404);
            }
        }

        if ($request->payment_method) {
            $input['payment_method'] = $request->payment_method;
        }

        $input['end_time'] = $end_time->format('H:i');

        $coupon = null;
        if ($request->coupon_id) {
            $coupon = $user->coupons()->where('coupon_id', $request->coupon_id)->first();

            if ($coupon) {
                return $this->respondErrorMessage(trans('messages.coupon_invalid'), 409);
            }

            $coupon = Coupon::find($request->coupon_id);
            if (!$this->isValidCoupon($coupon, $user, $request->all())) {
                return $this->respondErrorMessage(trans('messages.coupon_invalid'), 409);
            }
        }

        try {
            DB::beginTransaction();
            $order = $user->orders()->create($input);

            if ($coupon) {
                $order->coupon_id = $request->coupon_id;
                $order->coupon_name = $request->coupon_name;
                $order->coupon_type = $request->coupon_type;
                $order->coupon_value = $request->coupon_value;
                $order->coupon_max_point = $request->coupon_max_point;
                $order->save();

                $user->coupons()->attach($request->coupon_id, ['order_id' => $order->id]);
            }

            $listNomineeIds = explode(",", trim($request->nominee_ids, ","));

            $order->nominees()->attach($listNomineeIds, [
                'type' => CastOrderType::NOMINEE,
                'status' => CastOrderStatus::OPEN,
            ]);

            $ownerId = $order->user_id;
            $nominee = $order->nominees()->first();
            $room = $this->createDirectRoom($ownerId, $nominee->id);

            $order->room_id = $room->id;
            $order->save();

            $order->nominees()->updateExistingPivot(
                $nominee->id,
                [
                    'cost' => $nominee->cost,
                ],
                false
            );

            $offer->status = CastOfferStatus::APPROVED;
            $offer->save();

            $nominee->notify(
                (new CreateNominationOrdersForCast($order->id))->delay(now()->addSeconds(3))
            );

            $inviteCodeHistory = $user->inviteCodeHistory;
            if ($inviteCodeHistory) {
                if (InviteCodeHistoryStatus::PENDING == $inviteCodeHistory->status && null == $inviteCodeHistory->order_id) {
                    $inviteCodeHistory->order_id = $order->id;
                    $inviteCodeHistory->save();
                }
            }

            DB::commit();

            return $this->respondWithData(new OrderResource($order));
        } catch (\Exception $e) {
            DB::rollBack();
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }

    private function isValidCoupon($coupon, $user, $input)
    {
        if (!isset($input['coupon_max_point']) || 'null' == $input['coupon_max_point']) {
            $input['coupon_max_point'] = null;
        }

        $now = now();
        $createdAtOfUser = Carbon::parse($user->created_at);
        $isValid = true;
        if ($coupon->is_filter_after_created_date && $coupon->filter_after_created_date) {
            if ($now->diffInDays($createdAtOfUser) > $coupon->filter_after_created_date) {
                $isValid = false;
            }
        }

        if ($coupon->type != $input['coupon_type'] || trim($coupon->name) != trim($input['coupon_name']) || $coupon->max_point
            != $input['coupon_max_point']) {
            $isValid = false;
        }

        switch ($coupon->type) {
            case CouponType::POINT:
                if ($coupon->point != $input['coupon_value']) {
                    $isValid = false;
                }
                break;
            case CouponType::TIME:
                if ($coupon->time != $input['coupon_value']) {
                    $isValid = false;
                }
                break;
            case CouponType::PERCENT:
                if ($coupon->percent != $input['coupon_value']) {
                    $isValid = false;
                }
                break;
            default:break;
        }

        return $isValid;
    }
}
